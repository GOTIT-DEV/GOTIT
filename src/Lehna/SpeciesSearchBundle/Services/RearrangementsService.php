<?php

namespace Lehna\SpeciesSearchBundle\Services;
use Lehna\SpeciesSearchBundle\Services\QueryBuilderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Service comptage des réarrangements de taxonomie
 */
class RearrangementsService {
  private $entityManager; // database manager

  // Parameters
  private $parameters; // parametres POST
  private $reference;
  private $target;

  // Counting and indexing
  private $rawResults; // raw result table
  private $fwdCounter; // compteur référence -> méthodes
  private $revCounter; // compteur méthodes -> référence
  private $refIndex; // index des données brutes sur la référence
  private $compareIndex; // index des données brutes par méthodes

  /**
   * Constructeur
   */
  public function __construct(EntityManagerInterface $manager, QueryBuilderService $qbservice) {
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
      $counter[$m['id_dataset']][$m['id']]['libelle_motu'] = $m['libelle_motu'];
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
        return $row['num_motu'] . ":" . $row['id_ref'] . ":" . $row['id_dataset'] . ":" . $row['id_methode'];
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
        $targetCnt = count($targetSet[$reference['num_motu']]);
        if ($targetCnt == 1) {
          // target a un seul row : match
          $fwd['match'] += 1;
          $rev['match'] += 1;
        } elseif ($targetCnt > 1) {
          // target a plusieurs row : lump ou reshuffling
          $lump = true;
          // référence a plusieurs rows qui matchent avec target ?
          foreach ($targetSet[$reference['num_motu']] as $rev_row) {
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
          $targetCnt = count($targetSet[$ref_row['num_motu']]);
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
      $rawSql = "SELECT distinct Ass.num_motu,
                voc.code AS methode,
                voc.id AS id_methode,
                motu.id AS id_dataset,
                motu.date_motu,
                motu.libelle_motu AS libelle_motu,
                R.id AS id_ref,
                R.genus,
                R.species,
                R.taxname,

                Ass.sequence_assemblee_fk as seq,
                Ass.sequence_assemblee_ext_fk as seq_ext,
                sta.id as id_sta

                FROM Assigne Ass
                JOIN motu ON Ass.motu_fk=motu.id
                JOIN voc ON Ass.methode_motu_voc_fk=voc.id
                JOIN espece_identifiee Esp
                    ON Ass.sequence_assemblee_fk=Esp.sequence_assemblee_fk
                    OR Ass.sequence_assemblee_ext_fk=Esp.sequence_assemblee_ext_fk
                JOIN referentiel_taxon R ON Esp.referentiel_taxon_fk=R.id

                LEFT JOIN sequence_assemblee_ext sext ON Esp.sequence_assemblee_ext_fk=sext.id
                LEFT JOIN sequence_assemblee seq ON Esp.sequence_assemblee_fk=seq.id
                LEFT JOIN est_aligne_et_traite eat ON eat.sequence_assemblee_fk=seq.id
                LEFT JOIN chromatogramme chr ON chr.id = eat.chromatogramme_fk
                LEFT JOIN pcr ON chr.pcr_fk=pcr.id
                LEFT JOIN adn ON pcr.adn_fk=adn.id
                LEFT JOIN individu ind ON ind.id = adn.individu_fk
                LEFT JOIN lot_materiel lm ON ind.lot_materiel_fk=lm.id
                LEFT JOIN collecte co ON co.id = sext.collecte_fk OR co.id=lm.collecte_fk
                LEFT JOIN station sta ON co.station_fk = sta.id

                WHERE voc.code != 'HAPLO'
                AND motu.id = :target_dataset";
      if ($this->reference == 1) {
        // taxa filter
        $rawSql .= " AND R.id = :tax_id";
        $stmt = $pdo->prepare($rawSql);
        $stmt->execute(array(
          'tax_id'         => $this->parameters->get('taxname'),
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
                a1.num_motu as id_ref,
                a2.num_motu as num_motu,
                v2.code AS methode,
                v2.id AS id_methode,
                m2.id AS id_dataset,
                m2.libelle_motu AS libelle_motu,
                m2.date_motu AS date_motu,

                a1.sequence_assemblee_fk as seq,
                a1.sequence_assemblee_ext_fk as seq_ext,
                sta.id as id_sta

                FROM assigne a1
                JOIN motu m1 ON a1.motu_fk=m1.id
                JOIN voc v1 ON a1.methode_motu_voc_fk=v1.id
                JOIN assigne a2
                    ON a1.sequence_assemblee_fk=a2.sequence_assemblee_fk
                    OR a1.sequence_assemblee_ext_fk=a2.sequence_assemblee_ext_fk
                JOIN voc v2 ON a2.methode_motu_voc_fk=v2.id
                JOIN motu m2 ON m2.id=a2.motu_fk

                LEFT JOIN sequence_assemblee_ext sext ON a1.sequence_assemblee_ext_fk=sext.id
                LEFT JOIN sequence_assemblee seq ON a1.sequence_assemblee_fk=seq.id
                LEFT JOIN est_aligne_et_traite eat ON eat.sequence_assemblee_fk=seq.id
                LEFT JOIN chromatogramme chr ON chr.id = eat.chromatogramme_fk
                LEFT JOIN pcr ON chr.pcr_fk=pcr.id
                LEFT JOIN adn ON pcr.adn_fk=adn.id
                LEFT JOIN individu ind ON ind.id = adn.individu_fk
                LEFT JOIN lot_materiel lm ON ind.lot_materiel_fk=lm.id
                LEFT JOIN collecte co ON co.id = sext.collecte_fk OR co.id=lm.collecte_fk
                LEFT JOIN station sta ON co.station_fk = sta.id

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
      $this->compareIndex[$row['id_dataset']][$row['id_methode']][$row['num_motu']][] = $row;
    }
  }
}