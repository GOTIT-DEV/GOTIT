<?php

namespace Bbees\E3sBundle\Services;
use Bbees\E3sBundle\Services\QueryBuilderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Service QueryBuilderService
 */
class RearrangementsService {

  private $entityManager;
  private $request;
  private $qbservice;
  private $rawResults;
  private $fwdCounter;
  private $revCounter;
  private $refIndex;
  private $compareIndex;

  public function __construct(EntityManagerInterface $manager, QueryBuilderService $qbservice) {
    $this->entityManager = $manager;
    $this->qbservice = $qbservice;
    $this->fwdCounter = [];
    $this->revCounter = [];
    $this->initCounter($this->fwdCounter);
    $this->revCounter = $this->fwdCounter;
  }

  public function initCounter(&$counter) {
    $methodes = $this->qbservice->listMethodsByDate();
    $keys = ['match', 'split', 'lump', 'reshuffling'];
    foreach ($methodes as $m) {
      $counter[$m['id_date_motu']][$m['id']] = array_fill_keys($keys, 0); // stocke les comptages
      // Ajout d'infos sur la méthode
      $counter[$m['id_date_motu']][$m['id']]['methode'] = $m['code'];
      $counter[$m['id_date_motu']][$m['id']]['date_motu'] = $m['date_motu'];
      $counter[$m['id_date_motu']][$m['id']]['label'] = $m['code'] . ' ' . $m['date_motu']->format('Y');
      $counter[$m['id_date_motu']][$m['id']]['seq'] = [];
      $counter[$m['id_date_motu']][$m['id']]['seq_ext'] = [];
      $counter[$m['id_date_motu']][$m['id']]['sta'] = [];
    }
  }

  public function countSeqSta() {
    foreach ($this->rawResults as &$row) {
      $this->fwdCounter[$row['id_date_methode']][$row['id_methode']]['seq'][] = $row['seq'];
      $this->fwdCounter[$row['id_date_methode']][$row['id_methode']]['seq_ext'][] = $row['seq_ext'];
      $this->fwdCounter[$row['id_date_methode']][$row['id_methode']]['sta'][] = $row['id_sta'];
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

  public function simplify() {
    $this->rawResults = array_intersect_key($this->rawResults,
      array_unique(array_map(function ($row) {
        return $row['num_motu'] . ":" . $row['taxid'] . ":" . $row['id_date_methode'] . ":" . $row['id_methode'];
      }, $this->rawResults)));
  }


  public function compare() {
    foreach ($this->refIndex as $date => $methodes) {
      foreach ($methodes as $methode => $motus) {
        $counters = $this->compareSets($motus, $this->compareIndex[$date][$methode]);
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

  public function getResults() {
    //Restructuration des résultats de comptage
    $recto = [];
    foreach ($this->fwdCounter as $date => $methodes) {
      foreach ($methodes as $methode => $counts) {
        if ($this->request->get('reference') < 2 || ($date == $this->request->get('date_methode') && $methode != $this->request->get('methode'))) {
          $recto[] = $counts;
        }
      }
    }

    $verso = [];
    foreach ($this->fwdCounter as $date => $methodes) {
      foreach ($methodes as $methode => $counts) {
        if ($this->request->get('reference') < 2 || ($date == $this->request->get('date_methode') && $methode != $this->request->get('methode'))) {
          $verso[] = $counts;
        }
      }
    }
    return array(
      'recto' => $recto,
      'verso' => $verso,
    );
  }

  public function compareSets($refSet, $targetSet) {
    $fwd = array_fill_keys(['match', 'split', 'lump', 'reshuffling'], 0);
    $rev = array_fill_keys(['match', 'split', 'lump', 'reshuffling'], 0);
    foreach ($refSet as $ref_id => $reference) {
      if (count($reference) == 1) {
        // ref has 1 motu
        $motu = $reference[0]; // count taxons in the single motu
        $targetCnt = count($targetSet[$motu['num_motu']]);
        if ($targetCnt == 1) {
          // motu has 1 taxon : match
          $fwd['match'] += 1;
          $rev['match'] += 1;
        } elseif ($targetCnt > 1) {
          // ref has many motu : lump or reshuffle
          $reverseMotus = $targetSet[$motu['num_motu']];
          $lump = true; // lump if all taxon have only current motu, else reshuffle
          foreach ($reverseMotus as $revMotu) {
            $nb_motus = count($refSet[$revMotu['taxid']]);
            if ($nb_motus > 1) // at least one target has many motu
            {
              $lump = false;
            }
          }
          if ($lump) {
            // many targets, one ref
            $fwd['lump'] += 1;
          } else {
            // many targets, many refs
            $fwd['reshuffling'] += 1;
          }
          $rev['split'] += 1; // target is a split of reference anyway
        }
      } elseif (count($reference) > 1) {
        // taxon has many motus
        $lump = true;
        foreach ($reference as $motu) {
          // each motu is split or reshuffling
          $targetCnt = count($targetSet[$motu['num_motu']]);
          if ($targetCnt == 1) {
            // motu has only current taxon : split
            $fwd['split'] += 1;
          } elseif ($targetCnt > 1) {
            // many taxons, many motus : reshuffling
            $fwd['reshuffling'] += 1;
            $lump = false;
          }
        }
        if ($lump) {
          $rev['lump'] += 1;
        } else {
          $rev['reshuffling'] += 1;
        }
      }
    }

    return [$fwd, $rev];
  }

  public function fetch(ParameterBag $request) {
    $this->request = $request;
    $pdo = $this->entityManager->getConnection();
    // Référence : radio button coché (morpho, morpho filtré ou méthode)
    $reference = $request->get('reference'); // 0 : morpho, 1 : molecular

    if ($reference < 2) {
      // morpho
      $rawSql = "SELECT distinct Ass.num_motu,
                voc.code AS methode,
                voc.id AS id_methode,
                motu.id AS id_date_methode,
                motu.date_motu,
                R.id AS taxid,
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

                WHERE voc.code != 'HAPLO'";
      if ($reference == 1) {
        // taxa filter
        $rawSql .= " AND R.id = :tax_id";
        $stmt = $pdo->prepare($rawSql);
        $stmt->bindValue('tax_id', $request->get('taxname'));
      } else {
        $stmt = $pdo->prepare($rawSql);
      }

      $stmt->execute();
    } else {
      //molecular
      $id_date_motu = $request->get('date_methode');
      $id_methode = $request->get('methode');
      $rawSql = "SELECT distinct
                a1.num_motu as taxid,
                a2.num_motu as num_motu,
                v2.code AS methode,
                v2.id AS id_methode,
                m2.id AS id_date_methode,
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
                AND m2.id = :id_date_motu
                AND m1.id = m2.id
                AND v1.id = :id_methode
                AND m1.id = :id_date_motu";

      $stmt = $pdo->prepare($rawSql);
      $stmt->execute(array(
        'id_methode' => $id_methode,
        'id_date_motu' => $id_date_motu,
      ));
    }
    // Données brutes des séquences et assignations par méthode
    $this->rawResults = $stmt->fetchAll();
  }

  public function indexResults() {
    foreach ($this->rawResults as $row) {
      $this->refIndex[$row['id_date_methode']][$row['id_methode']][$row['taxid']][] = $row;
      $this->compareIndex[$row['id_date_methode']][$row['id_methode']][$row['num_motu']][] = $row;
    }
  }
}