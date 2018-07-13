<?php

namespace Bbees\E3sBundle\Controller\requetes;

use Bbees\E3sBundle\Entity\Motu;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Formulaire 2 controller.
 *
 * @Route("requetes/rearrangements")
 */
class RearrangementsController extends Controller {
  /**
   * @Route("/", name="rearrangements")
   */
  public function indexAction() {
    $service = $this->get('bbees_e3s.query_builder_e3s');
    $doctrine = $this->getDoctrine();
    $genus_set = $service->getGenusSet();
    $dates_methode = $doctrine->getRepository(Motu::class)->findAll();
    return $this->render('requetes/rearrangements/index.html.twig', array(
      'dates_methode' => $dates_methode,
      'genus_set' => $genus_set,
      "with_taxname" => true,
    ));
  }

  /**
   * @Route("/requete", name="requete2")
   */
  public function searchQuery(Request $request) {
    $pdo = $this->getDoctrine()->getManager()->getConnection();

    $reference = $request->request->get('reference'); // 0 : morpho, 1 : molecular

    if ($reference < 2) { // morpho
      $rawSql = "SELECT distinct Ass.num_motu,
                voc.code AS methode,
                voc.id AS id_methode,
                motu.id AS id_date_methode,
                motu.date_motu,
                R.id AS taxid,
                R.genus,
                R.species,
                R.taxname
                FROM Assigne Ass
                JOIN motu ON Ass.motu_fk=motu.id
                JOIN voc ON Ass.methode_motu_voc_fk=voc.id
                JOIN espece_identifiee Esp
                    ON Ass.sequence_assemblee_fk=Esp.sequence_assemblee_fk
                    OR Ass.sequence_assemblee_ext_fk=Esp.sequence_assemblee_ext_fk
                JOIN referentiel_taxon R ON Esp.referentiel_taxon_fk=R.id
                WHERE voc.code != 'HAPLO'";
      if ($reference == 1) { // taxa filter
        $rawSql .= " AND R.id = :tax_id";
        $stmt = $pdo->prepare($rawSql);
        $stmt->bindValue('tax_id', $request->request->get('taxname'));
      } else {
        $stmt = $pdo->prepare($rawSql);
      }

      $stmt->execute();
    } else { //molecular
      $id_date_motu = $request->request->get('date_methode');
      $id_methode = $request->request->get('methode');
      $rawSql = "SELECT distinct
                a1.num_motu as taxid,
                a2.num_motu as num_motu,
                v2.code AS methode,
                v2.id AS id_methode,
                m2.id AS id_date_methode,
                m2.date_motu AS date_motu
                FROM assigne a1
                JOIN motu m1 ON a1.motu_fk=m1.id
                JOIN voc v1 ON a1.methode_motu_voc_fk=v1.id
                JOIN assigne a2
                    ON a1.sequence_assemblee_fk=a2.sequence_assemblee_fk
                    OR a1.sequence_assemblee_ext_fk=a2.sequence_assemblee_ext_fk
                JOIN voc v2 ON a2.methode_motu_voc_fk=v2.id
                JOIN motu m2 ON m2.id=a2.motu_fk
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

    $result = $stmt->fetchAll();
    dump($result);

    $counters = $this->prepareMotuCountArray();
    if ($result) {
      $counters = $this->compareMotuSets($result, $counters);
    }
    $recto = $counters['forward'];
    $verso = $counters['reverse'];
    //dump($recto);

    $res = [];
    foreach ($recto as $id_date => $date_motu) {
      foreach ($date_motu as $id_m => $methode) {
        if ($reference < 2 || ($id_date == $id_date_motu && $id_m != $id_methode)) {
          $res[] = $methode;
        }

      }
    }
    $recto = $res;

    $res = [];
    foreach ($verso as $id_date => $date_motu) {
      foreach ($date_motu as $id_m => $methode) {
        if ($reference < 2 || ($id_date == $id_date_motu && $id_m != $id_methode)) {
          $res[] = $methode;
        }

      }
    }
    $verso = $res;

    return new JsonResponse(array(
      'recto' => $recto,
      'verso' => $verso,
    ));
  }

  private function compareMethodSets($methodSet1, $methodSet2, $counters) {
    $motuCounter = array_fill_keys(['match', 'split', 'lump', 'reshuffling'], 0);
    $revCounter = array_fill_keys(['match', 'split', 'lump', 'reshuffling'], 0);

    foreach ($methodSet1 as $ref_id => $motuSet) {

      if (count($motuSet) == 1) { // taxon has 1 motu
        $motu = $motuSet[0]; // count taxons in the single motu
        $reverseCnt = count($methodSet2[$motu['num_motu']]);
        if ($reverseCnt == 1) { // motu has 1 taxon : match
          $motuCounter['match'] += 1;
          $revCounter['match'] += 1;

        } elseif ($reverseCnt > 1) { // motu has many taxon : lump or reshuffle
          $reverseMotus = $methodSet2[$motu['num_motu']];
          $lump = true; // lump if all taxon have only current motu, else reshuffle
          foreach ($reverseMotus as $revMotu) {
            $nb_motus = count($methodSet1[$revMotu['taxid']]);
            if ($nb_motus > 1) // at least one taxon has many motu
            {
              $lump = false;
            }

          }
          if ($lump) { // many taxons, one motu
            $motuCounter['lump'] += 1;
          } else { // many taxons, many motu
            $motuCounter['reshuffling'] += 1;
          }

          $revCounter['split'] += 1; // taxon is a split of motu anyway

        }
      } elseif (count($motuSet) > 1) { // taxon has many motus
        $lump = true;
        foreach ($motuSet as $motu) { // each motu is split or reshuffling
          $reverseCnt = count($methodSet2[$motu['num_motu']]);

          if ($reverseCnt == 1) { // motu has only current taxon : split
            $motuCounter['split'] += 1;
          } elseif ($reverseCnt > 1) { // many taxons, many motus : reshuffling
            $motuCounter['reshuffling'] += 1;
            $lump = false;
          }

        }
        if ($lump) {
          $revCounter['lump'] += 1;
        } else {
          $revCounter['reshuffling'] += 1;
        }
      }
    }

    // $revCounter[$date][$meth] = array_merge($revCounter[$date][$meth], $counters[1]);
    return [$motuCounter, $revCounter];
  }

  private function compareMotuSets($rows, $counters) {
    /* Count split, shift, lumps and reshuffling in
    morphological species caused by each molecular method

    motuSets is a dictionnary used to group results by taxid and motuid,
    separating between method+datemethode.
    It allows to count the motu asigned to one taxon in each method,
    and the other way around. Used to do set comparisons and find
    split lumps match and reshufflings. Looks like :
    'ref' :
    taxon_id :
    date_methode_id :
    methode_id :
    taxid: [    // array of
    'id' : taxon id,
    'num_motu' : motu id,
    'id_methode : method id,
    'id_date_methode' : date motu id,
    ]
    date_motu_id :
    method_id:
    motu id: [    // array of
    'id' : taxon id,
    'num_motu' : motu id,
    'id_methode : method id,
    'id_date_methode' : date motu id,
    ]

    motuCounter counts each split match lump reshuffling,
    grouping by methode. Access is :
    date_methode_id :
    methode_id :
    ['split', 'match', 'lump', 'reshuffling']

     */
    $motuSets = $this->buildComparisonSets($rows);
    dump($motuSets);
    $taxonSet = $motuSets['ref'];
    $motuSet = $motuSets['motus'];
    foreach ($taxonSet as $date => $methodes) {
      foreach ($methodes as $meth => $motus) {
        $methCounters = $this->compareMethodSets($motus, $motuSet[$date][$meth], $counters);
        $counters['forward'][$date][$meth] = array_merge(
          $counters['forward'][$date][$meth],
          $methCounters[0]
        );
        $counters['reverse'][$date][$meth] = array_merge(
          $counters['reverse'][$date][$meth],
          $methCounters[1]
        );
      }
    }

    return $counters;
  }

  private function buildComparisonSets($rows) {
    //dump($rows);
    $ordered = array();
    foreach ($rows as $row) {
      $ordered['ref'][$row['id_date_methode']][$row['id_methode']][$row['taxid']][] = $row;
      $ordered['motus'][$row['id_date_methode']][$row['id_methode']][$row['num_motu']][] = $row;
    }

    return $ordered;
  }

  private function prepareMotuCountArray() {
    $service = $this->get('bbees_e3s.query_builder_e3s');
    $methodes = $service->listMethodsByDate();
    $resultArray = array();
    $keys = ['match', 'split', 'lump', 'reshuffling'];
    foreach ($methodes as $m) {
      $resultArray[$m['id_date_motu']][$m['id']] = array_fill_keys($keys, 0);
      $resultArray[$m['id_date_motu']][$m['id']]['methode'] = $m['code'];
      $resultArray[$m['id_date_motu']][$m['id']]['date_motu'] = $m['date_motu'];
      $resultArray[$m['id_date_motu']][$m['id']]['label'] = $m['code'] . ' ' . $m['date_motu']->format('Y');
    }
    $reverseArray = $resultArray;
    return [
      'forward' => $resultArray,
      'reverse' => $reverseArray,
    ];
  }

}
