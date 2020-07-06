<?php

namespace App\Services\SpeciesSearch;
use App\Services\SpeciesSearch\SpeciesQueryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Species hypotheses rearrangements service
 */
class SpeciesHypothesesService {
  private $entityManager; // database manager

  // Parameters
  private $parameters; // POST params
  private $reference;
  private $target;

  // Counting and indexing
  private $rawResults; // raw result table
  private $fwdCounter; // (reference -> methods) counter
  private $revCounter; // (methods -> reference) counter
  private $refIndex; // raw data indexing with keys = reference
  private $compareIndex; // raw data indexing with keys = methods

  /**
   * Constructeur
   */
  public function __construct(EntityManagerInterface $manager, SpeciesQueryService $qbservice) {
    $this->entityManager = $manager;
    $this->qbservice     = $qbservice;
    $this->fwdCounter    = [];
    $this->revCounter    = [];
  }

  public function processQuery(ParameterBag $parameters) {
    $this->setParameters($parameters);
    $this->fetch();
    if (!$this->rawResults) {
      return ['recto' => [], 'verso' => []];
    }
    $this->countSeqSta();
    $this->indexResults();
    $this->compare();
    return $this->getResults();
  }

  public function setParameters(ParameterBag $parameters) {
    $this->parameters = $parameters;
    $this->reference  = $parameters->get('reference');
    if ($this->reference < 2) {
      $this->target = $parameters->get('target-dataset');
    } else {
      $this->target = $parameters->get('dataset');
    }
    $this->initCounter($this->fwdCounter);
    $this->initCounter($this->revCounter);
  }

  /**
   * Initialize les compteurs de réarrangement par méthode
   */
  public function initCounter(&$counter) {
    $methodes = $this->qbservice->getMethodsByDate($this->target); // liste des méthodes
    $keys     = ['match', 'split', 'lump', 'reshuffling']; // clés de comptage
    foreach ($methodes as $m) {
      $counter[$m['id_dataset']][$m['id']] = array_fill_keys($keys, 0); // stocke les comptages
      // Ajout d'infos sur la méthode
      $counter[$m['id_dataset']][$m['id']]['methode']      = $m['code'];
      $counter[$m['id_dataset']][$m['id']]['motu_title'] = $m['motu_title'];
      // Liste des séquences et stations à compter
      $counter[$m['id_dataset']][$m['id']]['seq']     = [];
      $counter[$m['id_dataset']][$m['id']]['seq_ext'] = [];
      $counter[$m['id_dataset']][$m['id']]['sta']     = [];
    }
  }

  /**
   * Compte les séquences et les stations par méthode sur la base des listes
   */
  public function countSeqSta() {
    foreach ($this->rawResults as &$row) {
      $this->fwdCounter[$row['id_dataset']][$row['id_methode']]['seq'][]     = $row['seq'];
      $this->fwdCounter[$row['id_dataset']][$row['id_methode']]['seq_ext'][] = $row['seq_ext'];
      $this->fwdCounter[$row['id_dataset']][$row['id_methode']]['sta'][]     = $row['id_sta'];
      unset($row['seq']);
      unset($row['seq_ext']);
      unset($row['sta']);
    }
    foreach ($this->fwdCounter as $date => &$methodes) {
      foreach ($methodes as $method => &$counts) {
        $counts['nb_seq'] =
        count(array_filter(array_unique($counts['seq']))) +
        count(array_filter(array_unique($counts['seq_ext'])));
        $counts['nb_sta'] = count(array_filter(array_unique($counts['sta'])));
        unset($counts['seq']);
        unset($counts['seq_ext']);
        unset($counts['sta']);
      }
    }
    $this->revCounter = $this->fwdCounter;
    $this->simplify();
  }

  /**
   * Supprime les lignes en double générées par les stations/séquences
   */
  public function simplify() {
    $this->rawResults = array_intersect_key($this->rawResults,
      array_unique(array_map(function ($row) {
        return $row['motu_number'] . ":" . $row['id_ref'] . ":" . $row['id_dataset'] . ":" . $row['id_methode'];
      }, $this->rawResults)));
  }

  /**
   * Comparer les références par méthode à toutes les autres méthodes
   */
  public function compare() {
    foreach ($this->refIndex as $date => $methodes) {
      foreach ($methodes as $methode => $motus) {
        // Comptage
        $counters = $this->compareSets($motus, $this->compareIndex[$date][$methode]);
        // Merge des comptages dans les compteurs globaux
        $this->fwdCounter[$date][$methode] = array_merge(
          $this->fwdCounter[$date][$methode],
          $counters[0]
        );
        $this->revCounter[$date][$methode] = array_merge(
          $this->revCounter[$date][$methode],
          $counters[1]
        );
      }
    }
  }

  /**
   * Comptage des réarrangements entre l'ensemble de référence pour une méthode
   * et l'ensemble à comparer, pour une méthode
   */
  public function compareSets($refSet, $targetSet) {
    $fwd = array_fill_keys(['match', 'split', 'lump', 'reshuffling'], 0);
    $rev = array_fill_keys(['match', 'split', 'lump', 'reshuffling'], 0);
    foreach ($refSet as $ref_id => $ref_rows) {
      if (count($ref_rows) == 1) {
        // référence a un seul row
        $reference = $ref_rows[0];
        // nombre de rows dans l'ensemble à comparer qui matchent la référence
        $targetCnt = count($targetSet[$reference['motu_number']]);
        if ($targetCnt == 1) {
          // target a un seul row : match
          $fwd['match'] += 1;
          $rev['match'] += 1;
        } elseif ($targetCnt > 1) {
          // target a plusieurs row : lump ou reshuffling
          $lump = true;
          // référence a plusieurs rows qui matchent avec target ?
          foreach ($targetSet[$reference['motu_number']] as $rev_row) {
            $nb_motus = count($refSet[$rev_row['id_ref']]);
            if ($nb_motus > 1) {
              $lump = false;
            }
          }
          $type = $lump ? 'lump' : 'reshuffling';
          $fwd[$type] += 1;
          $rev['split'] += 1; // target est un split de la référence
        }
      } elseif (count($ref_rows) > 1) {
        // référence a plusieurs rows
        $lump = true;
        foreach ($ref_rows as $ref_row) {
          // chaque réf est un split ou un reshuffling
          $targetCnt = count($targetSet[$ref_row['motu_number']]);
          if ($targetCnt == 1) {
            // un seul row dans target : split
            $fwd['split'] += 1;
          } elseif ($targetCnt > 1) {
            // many réf, many target : reshuffling
            $fwd['reshuffling'] += 1;
            $lump = false;
          }
        }
        $type = $lump ? 'lump' : 'reshuffling';
        $rev[$type] += 1;
      }
    }
    return [$fwd, $rev];
  }

  /**
   * Filtre les résultats de comptage en fonction de la référence
   */
  public function filterCounts($counter) {
    $filtered = [];
    foreach ($counter as $date => $methodes) {
      foreach ($methodes as $methode => $counts) {
        // Retenir les résultats du même dataset et méthode différente de la référence
        if ($this->parameters->get('reference') < 2 || // pas de filtre si ref morpho
          ($date == $this->parameters->get('dataset') &&
            $methode != $this->parameters->get('methode'))) {
          $filtered[] = $counts;
        }
      }
    }
    return $filtered;
  }

  /**
   * Construit le contenu de la référence JSON
   */
  public function getResults() {
    // Filtrage des comptages
    return array(
      'recto' => $this->filterCounts($this->fwdCounter),
      'verso' => $this->filterCounts($this->revCounter),
    );
  }

  /**
   * Execute la requête pour obtenir les résultats bruts sur les séquences et leur MOTU
   */
  public function fetch() {
    $pdo = $this->entityManager->getConnection();

    if ($this->reference < 2) {
      // morpho
      $rawSql = "SELECT distinct motu_nb.motu_number,
                vocabulary.code AS methode,
                vocabulary.id AS id_methode,
                motu.id AS id_dataset,
                motu.motu_date,
                motu.motu_title AS motu_title,
                R.id AS id_ref,
                R.genus,
                R.species,
                R.taxon_name,

                motu_nb.internal_sequence_fk as seq,
                motu_nb.external_sequence_fk as seq_ext,
                sta.id as id_sta

                FROM motu_number AS motu_nb
                JOIN motu ON motu_nb.motu_fk=motu.id
                JOIN vocabulary ON motu_nb.delimitation_method_voc_fk=vocabulary.id
                JOIN identified_species Esp
                    ON motu_nb.internal_sequence_fk=Esp.internal_sequence_fk
                    OR motu_nb.external_sequence_fk=Esp.external_sequence_fk
                JOIN taxon R ON Esp.taxon_fk=R.id

                LEFT JOIN external_sequence sext ON Esp.external_sequence_fk=sext.id
                LEFT JOIN internal_sequence seq ON Esp.internal_sequence_fk=seq.id
                LEFT JOIN chromatogram_is_processed_to eat ON eat.internal_sequence_fk=seq.id
                LEFT JOIN chromatogram chr ON chr.id = eat.chromatogram_fk
                LEFT JOIN pcr ON chr.pcr_fk=pcr.id
                LEFT JOIN dna ON pcr.dna_fk=dna.id
                LEFT JOIN specimen ind ON ind.id = dna.specimen_fk
                LEFT JOIN internal_biological_material lm ON ind.internal_biological_material_fk=lm.id
                LEFT JOIN sampling co ON co.id = sext.sampling_fk OR co.id=lm.sampling_fk
                LEFT JOIN site sta ON co.site_fk = sta.id

                WHERE vocabulary.code != 'HAPLO'
                AND motu.id = :target_dataset";
      if ($this->reference == 1) {
        // taxa filter
        $rawSql .= " AND R.id = :tax_id";
        $stmt = $pdo->prepare($rawSql);
        $stmt->execute(array(
          'tax_id'         => $this->parameters->get('taxon_name'),
          'target_dataset' => $this->target,
        ));
      } else {
        $stmt = $pdo->prepare($rawSql);
        $stmt->execute(array(
          'target_dataset' => $this->target,
        ));
      }

    } else {
      //molecular
      $id_dataset = $this->parameters->get('dataset');
      $id_methode = $this->parameters->get('methode');
      $rawSql     = "SELECT distinct
                a1.motu_number as id_ref,
                a2.motu_number as motu_number,
                v2.code AS methode,
                v2.id AS id_methode,
                m2.id AS id_dataset,
                m2.motu_title AS motu_title,
                m2.motu_date AS motu_date,

                a1.internal_sequence_fk as seq,
                a1.external_sequence_fk as seq_ext,
                sta.id as id_sta

                FROM motu_number a1
                JOIN motu m1 ON a1.motu_fk=m1.id
                JOIN vocabulary v1 ON a1.delimitation_method_voc_fk=v1.id
                JOIN motu_number a2
                    ON a1.internal_sequence_fk=a2.internal_sequence_fk
                    OR a1.external_sequence_fk=a2.external_sequence_fk
                JOIN vocabulary v2 ON a2.delimitation_method_voc_fk=v2.id
                JOIN motu m2 ON m2.id=a2.motu_fk

                LEFT JOIN external_sequence sext ON a1.external_sequence_fk=sext.id
                LEFT JOIN internal_sequence seq ON a1.internal_sequence_fk=seq.id
                LEFT JOIN chromatogram_is_processed_to eat ON eat.internal_sequence_fk=seq.id
                LEFT JOIN chromatogram chr ON chr.id = eat.chromatogram_fk
                LEFT JOIN pcr ON chr.pcr_fk=pcr.id
                LEFT JOIN dna ON pcr.dna_fk=dna.id
                LEFT JOIN specimen ind ON ind.id = dna.specimen_fk
                LEFT JOIN internal_biological_material lm ON ind.internal_biological_material_fk=lm.id
                LEFT JOIN sampling co ON co.id = sext.sampling_fk OR co.id=lm.sampling_fk
                LEFT JOIN site sta ON co.site_fk = sta.id

                WHERE v1.code != 'HAPLO'
                AND v2.code !='HAPLO'
                AND v2.id != :id_methode
                AND m2.id = :target_dataset
                AND m1.id = m2.id
                AND v1.id = :id_methode
                AND m1.id = :id_dataset";

      $stmt = $pdo->prepare($rawSql);
      $stmt->execute(array(
        'id_methode'     => $id_methode,
        'id_dataset'     => $id_dataset,
        'target_dataset' => $this->target,
      ));
    }
    // Données brutes des séquences et assignations par méthode
    $this->rawResults = $stmt->fetchAll();
  }

  /**
   * Indexation des résultats bruts selon la référence et selon chaque méthode
   */
  public function indexResults() {
    foreach ($this->rawResults as $row) {
      $this->refIndex[$row['id_dataset']][$row['id_methode']][$row['id_ref']][]       = $row;
      $this->compareIndex[$row['id_dataset']][$row['id_methode']][$row['motu_number']][] = $row;
    }
  }
}