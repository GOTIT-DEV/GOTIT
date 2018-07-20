<?php

namespace Bbees\E3sBundle\Services;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Service QueryBuilderService
 */
class QueryBuilderService {
  private $entityManager;

  public function __construct(EntityManagerInterface $manager) {
    $this->entityManager = $manager;
  }

  /***************************************************************************
   * UTILITY QUERIES
   ***************************************************************************/

  public function getGenusSet() {
    $qb    = $this->entityManager->createQueryBuilder();
    $query = $qb->select('rt.genus')
      ->from('BbeesE3sBundle:ReferentielTaxon', 'rt')
      ->where('rt.genus IS NOT NULL')
      ->distinct()
      ->orderBy('rt.genus')
      ->getQuery();
    return $query->getResult();
  }

  public function getMethodsByDate($id_date_motu) {
    $qb    = $this->entityManager->createQueryBuilder();
    $query = $qb->select('v')
      ->from('BbeesE3sBundle:Motu', 'm')
      ->join('BbeesE3sBundle:Assigne', 'a', 'WITH', 'a.motuFk=m')
      ->join('BbeesE3sBundle:Voc', 'v', 'WITH', "a.methodeMotuVocFk=v AND v.code != 'HAPLO'")
      ->andWhere('m.id = :date_methode')
      ->setParameter('date_methode', $id_date_motu)
      ->distinct()
      ->getQuery();

    return $query->getArrayResult();
  }

  public function getMethod($id_methode, $id_date_methode) {
    $qb    = $this->entityManager->createQueryBuilder();
    $query = $qb->select('v.id as id_methode, v.code, m.id as id_date_methode, m.dateMotu as date_methode')
      ->from('BbeesE3sBundle:Motu', 'm')
      ->join('BbeesE3sBundle:Assigne', 'a', 'WITH', 'a.motuFk=m')
      ->join('BbeesE3sBundle:Voc', 'v', 'WITH', "a.methodeMotuVocFk=v AND v.code != 'HAPLO'")
      ->andWhere('m.id = :id_date_methode AND v.id = :id_methode')
      ->setParameters(array(
        ':id_date_methode' => $id_date_methode,
        ':id_methode'      => $id_methode,
      ))
      ->distinct()
      ->getQuery();

    return $query->getArrayResult()[0];
  }

  public function listMethodsByDate() {
    $qb    = $this->entityManager->createQueryBuilder();
    $query = $qb->select('v.id, v.code, m.id as id_date_motu, m.dateMotu as date_motu')
      ->from('BbeesE3sBundle:Motu', 'm')
      ->join('BbeesE3sBundle:Assigne', 'a', 'WITH', 'a.motuFk=m')
      ->join('BbeesE3sBundle:Voc', 'v', 'WITH', "a.methodeMotuVocFk=v AND v.code != 'HAPLO'")
      ->distinct()
      ->orderBy('m.id, v.id')
      ->getQuery();

    return $query->getArrayResult();
  }

  /****************************************************************************
   * JOINERS
   ****************************************************************************/

  private function joinIndivSeq($query, $indivAlias, $seqAlias) {
    return $query->join('BbeesE3sBundle:Adn', 'adn', 'WITH', "$indivAlias.id = adn.individuFk")
      ->join('BbeesE3sBundle:Pcr', 'pcr', 'WITH', 'adn.id = pcr.adnFk')
      ->join('BbeesE3sBundle:Chromatogramme', 'ch', 'WITH', 'pcr.id = ch.pcrFk')
      ->join('BbeesE3sBundle:EstAligneEtTraite', 'at', 'WITH', 'at.chromatogrammeFk = ch.id')
      ->join('BbeesE3sBundle:Assigne', 'ass', 'WITH', 'ass.sequenceAssembleeFk = at.sequenceAssembleeFk')
      ->join('BbeesE3sBundle:SequenceAssemblee', $seqAlias, 'WITH', "$seqAlias.id = at.sequenceAssembleeFk")
      ->join('BbeesE3sBundle:Voc', 'vocGene', 'WITH', 'vocGene.id = pcr.geneVocFk');
  }

  private function leftJoinIndivSeq($query, $indivAlias, $seqAlias) {
    return $query->leftJoin('BbeesE3sBundle:Adn', 'adn', 'WITH', "$indivAlias.id = adn.individuFk")
      ->leftJoin('BbeesE3sBundle:Pcr', 'pcr', 'WITH', 'adn.id = pcr.adnFk')
      ->leftJoin('BbeesE3sBundle:Chromatogramme', 'ch', 'WITH', 'pcr.id = ch.pcrFk')
      ->leftJoin('BbeesE3sBundle:EstAligneEtTraite', 'at', 'WITH', 'at.chromatogrammeFk = ch.id')
      ->leftJoin('BbeesE3sBundle:Assigne', 'ass', 'WITH', 'ass.sequenceAssembleeFk = at.sequenceAssembleeFk')
      ->leftJoin('BbeesE3sBundle:SequenceAssemblee', $seqAlias, 'WITH', "$seqAlias.id = at.sequenceAssembleeFk")
      ->leftJoin('BbeesE3sBundle:Voc', 'vocGene', 'WITH', 'vocGene.id = pcr.geneVocFk');
  }

  private function joinEspeceStation($query, $aliasEsp, $aliasSta) {
    return $query->leftJoin('BbeesE3sBundle:LotMateriel', 'lm', 'WITH', $aliasEsp . '.lotMaterielFk=lm.id')
      ->leftJoin('BbeesE3sBundle:LotMaterielExt', 'lmext', 'WITH', $aliasEsp . '.lotMaterielExtFk=lmext.id')
      ->join('BbeesE3sBundle:Collecte', 'c', 'WITH', 'c.id=lm.collecteFk OR c.id=lmext.collecteFk')
      ->join('BbeesE3sBundle:Station', $aliasSta, 'WITH', $aliasSta . '.id=c.stationFk');
  }

  private function joinMotuCountMorpho($query, $alias = 'ass') {
    return $query->leftJoin('BbeesE3sBundle:SequenceAssembleeExt', 'motu_sext', 'WITH', "motu_sext.id=$alias.sequenceAssembleExtFk")
      ->leftJoin('BbeesE3sBundle:EstAligneEtTraite', 'motu_at', 'WITH', "motu_at.sequenceAssembleeFk = $alias.sequenceAssembleeFk")
      ->leftJoin('BbeesE3sBundle:Chromatogramme', 'motu_chr', 'WITH', "motu_chr.id = motu_at.chromatogrammeFk")
      ->leftJoin('BbeesE3sBundle:Pcr', 'motu_pcr', 'WITH', "motu_pcr.id = motu_chr.pcrFk")
      ->leftJoin('BbeesE3sBundle:Adn', 'motu_adn', 'WITH', "motu_adn.id = motu_pcr.adnFk")
      ->leftJoin('BbeesE3sBundle:Individu', 'motu_ind', 'WITH', "motu_ind.id = motu_adn.individuFk")
      ->join('BbeesE3sBundle:EspeceIndividu', 'motu_eid', 'WITH', "eid.individuFk = motu_ind.id OR eid.sequenceAssembleeExtFk=motu_sext.id")
      ->join('BbeesE3sBundle:Voc', 'motu_voc', 'WITH', "motu_voc.id = $alias.methodeMotuVocFk")
      ->join('BbeesE3sBundle:Motu', 'motu_date', 'WITH', "motu_date.id = $alias.motuFk");

  }

  /*****************************************************************************
   * QUERIES
   *****************************************************************************/
  public function getMotuCountList($data) {

    $niveau       = $data->get('niveau');
    $methodes     = $data->get('methodes');
    $date_methode = $data->get('date_methode');
    $criteres     = $data->get('criteres');

    $qb    = $this->entityManager->createQueryBuilder();
    $query = $qb->select('rt.taxname, rt.id')
      ->addSelect('voc.id as id_methode, voc.code as methode')
      ->addSelect('motu.id as id_date_motu, motu.dateMotu as date_motu')
      ->addSelect('COUNT(DISTINCT ass.numMotu ) as nb_motus')
      ->from('BbeesE3sBundle:ReferentielTaxon', 'rt')
      ->join('BbeesE3sBundle:EspeceIdentifiee', 'e', 'WITH', 'rt.id = e.referentielTaxonFk');
    switch ($niveau) {
    case 1: #lot matériel
      $query = $query->join('BbeesE3sBundle:LotMateriel', 'lm', 'WITH', 'lm.id=e.lotMaterielFk')
        ->join('BbeesE3sBundle:Individu', 'i', 'WITH', 'i.lotMaterielFk = lm.id');
      $query = $this->joinIndivSeq($query, 'i', 'seq')->addSelect('COUNT(DISTINCT seq.id) as nb_seq');
      break;

    case 2: #individu
      $query = $query->join('BbeesE3sBundle:Individu', 'i', 'WITH', 'i.id = e.individuFk');
      $query = $this->joinIndivSeq($query, 'i', 'seq')->addSelect('COUNT(DISTINCT seq.id) as nb_seq');
      break;

    case 3: # sequence
      $query = $query->leftJoin('BbeesE3sBundle:SequenceAssemblee', 'seq', 'WITH', 'seq.id=e.sequenceAssembleeFk')
        ->leftJoin('BbeesE3sBundle:SequenceAssembleeExt', 'seqext', 'WITH', 'seqext.id=e.sequenceAssembleeExtFk')
        ->join('BbeesE3sBundle:Assigne', 'ass', 'WITH', 'ass.sequenceAssembleeExtFk=seqext.id OR ass.sequenceAssembleeFk=seq.id')
        ->addSelect('(COUNT(DISTINCT seq.id) + COUNT(DISTINCT seqext.id)) as nb_seq');
      break;
    }

    $query = $query->join('BbeesE3sBundle:Motu', 'motu', 'WITH', 'ass.motuFk = motu.id')
      ->join('BbeesE3sBundle:Voc', 'voc', 'WITH', 'ass.methodeMotuVocFk = voc.id');

    if ($data->get('taxaFilter')) {
      $query = $query->andWhere('rt.species = :species')
        ->andWhere('rt.genus = :genus')
        ->setParameters([
          'genus'   => $data->get('genus'),
          'species' => $data->get('species'),
        ]);
    }

    if ($criteres) {
      $query = $query->andWhere('e.critereIdentificationVocFk IN(:criteres)')
        ->setParameter('criteres', $criteres);
    }

    if ($methodes) {
      $query = $query->andWhere('ass.methodeMotuVocFk IN(:methodes)')
        ->setParameter('methodes', $methodes);
    }

    $query = $query->andWHere("voc.code != 'HAPLO'")
      ->andWhere('motu.id = :id_date_motu')
      ->setParameter('id_date_motu', $date_methode)
      ->groupBy('rt.id, rt.taxname, voc.id, voc.code, motu.id')
      ->orderBy('rt.id')
      ->getQuery();

    return $query->getArrayResult();
  }

  public function getSpeciesAssignment($data) {
    $columnsMap = array(
      'lot-materiel' => 'lmrt',
      'individu'     => 'indivrt',
      'sequence'     => 'seqrt',
    );
    $typeConstraints = array_fill_keys(
      ["A", "B", "C"],
      array());
    $undefinedSeq = ($data->get('sequence') == '1');
    foreach ($data as $key => $value) {
      if (in_array($value, array_keys($typeConstraints))) {
        $typeConstraints[$value][] = $columnsMap[$key];
      }
    }
    dump($typeConstraints);
    dump($undefinedSeq);

    $qb    = $this->entityManager->createQueryBuilder();
    $query = $qb->select('lm.id as id_lm, lm.codeLotMateriel as code_lm') // lot matériel
      ->addSelect('lmrt.id as idtax_lm, lmrt.taxname as taxname_lm') // taxon lot matériel
      ->addSelect('lmvoc.code as critere_lm') // critere lot matériel
      ->addSelect('indiv.id as id_indiv, indiv.codeIndBiomol as code_biomol, indiv.codeIndTriMorpho as code_tri_morpho') // individu
      ->addSelect('indivrt.id as idtax_indiv, indivrt.taxname as taxname_indiv') // taxon individu
      ->addSelect('ivoc.code as critere_indiv') // critere individu
      ->addSelect('seq.id as id_seq, seq.codeSqcAss as code_seq') // séquence
      ->addSelect('seqrt.id as idtax_seq, seqrt.taxname as taxname_seq') // taxon séquence
      ->addSelect('seqvoc.code as critere_seq') // critere sequence
    // JOIN lot matériel
      ->from('BbeesE3sBundle:LotMateriel', 'lm')
      ->join('BbeesE3sBundle:EspeceIdentifiee', 'eidlm', 'WITH', 'lm.id = eidlm.lotMaterielFk')
      ->join('BbeesE3sBundle:ReferentielTaxon', 'lmrt', 'WITH', 'lmrt.id = eidlm.referentielTaxonFk')
      ->join('BbeesE3sBundle:Voc', 'lmvoc', 'WITH', 'eidlm.critereIdentificationVocFk=lmvoc.id')
    // JOIN individu
      ->join('BbeesE3sBundle:Individu', 'indiv', 'WITH', 'indiv.lotMaterielFk = lm.id')
      ->join('BbeesE3sBundle:EspeceIdentifiee', 'eidindiv', 'WITH', 'indiv.id = eidindiv.individuFk')
      ->join('BbeesE3sBundle:ReferentielTaxon', 'indivrt', 'WITH', 'indivrt.id = eidindiv.referentielTaxonFk')
      ->join('BbeesE3sBundle:Voc', 'ivoc', 'WITH', 'eidindiv.critereIdentificationVocFk=ivoc.id')
    ;
    // JOIN sequence
    $query = $this->leftJoinIndivSeq($query, 'indiv', 'seq')
      ->leftJoin('BbeesE3sBundle:EspeceIdentifiee', 'eidseq', 'WITH', 'seq.id = eidseq.sequenceAssembleeFk')
      ->leftJoin('BbeesE3sBundle:ReferentielTaxon', 'seqrt', 'WITH', 'seqrt.id = eidseq.referentielTaxonFk')
      ->leftJoin('BbeesE3sBundle:Voc', 'seqvoc', 'WITH', 'eidseq.critereIdentificationVocFk=seqvoc.id')
    ;
    if ($undefinedSeq) {
      $query=$query->andWhere('seq.id IS NULL');
    }
    // FILTER based on user defined constraints
    $visited = [];
    foreach ($typeConstraints as $type => $identicals) {
      if ($identicals) {
        $current  = [];
        //dump($visited);
        $refTable = $identicals[0];
        foreach ($identicals as $tableAlias) {
          $current[] = $tableAlias;
          if ($tableAlias != $refTable) {
            $query = $query->andWhere("$refTable.id = $tableAlias.id");
            dump("$refTable = $tableAlias");
          }
          foreach($visited as $different){
            $query = $query->andWhere("$tableAlias.id != $different.id"); 
            dump("$tableAlias != $different");
          }
        }
        //dump($current);
        $visited = array_merge($current, $visited);
      }
    }
    $query = $query->distinct()->getQuery();
    $res   = $query->getArrayResult();
    return $res;
  }

  public function getMotuSeqList($data) {
    $id_taxon     = $data->get('taxon');
    $id_methode   = $data->get('methode');
    $date_methode = $data->get('date_motu');
    $niveau       = $data->get('niveau');
    $criteres     = $data->get('criteres');

    $qb    = $this->entityManager->createQueryBuilder();
    $query = $qb->select('rt.id as idesp, rt.taxname')
      ->addSelect('voc.code as methode')
      ->addSelect('m.dateMotu as date_motu')
      ->addSelect('seq.id, seq.codeSqcAss as code, seq.accessionNumber as acc')
      ->addSelect('ass.numMotu as motu')
      ->addSelect('v.code as critere')
      ->addSelect('vocGene.code as gene')
      ->from('BbeesE3sBundle:ReferentielTaxon', 'rt')
      ->join('BbeesE3sBundle:EspeceIdentifiee', 'e', 'WITH', 'rt.id = e.referentielTaxonFk')
      ->join('BbeesE3sBundle:Voc', 'v', 'WITH', 'e.critereIdentificationVocFk=v.id');
    switch ($niveau) {
    case 1: #lot matériel
      $query = $query->join('BbeesE3sBundle:LotMateriel', 'lm', 'WITH', 'lm.id=e.lotMaterielFk')
        ->join('BbeesE3sBundle:Individu', 'i', 'WITH', 'i.lotMaterielFk = lm.id');
      $query = $this->joinIndivSeq($query, 'i', 'seq');
      break;

    case 2: #individu
      $query = $query->join('BbeesE3sBundle:Individu', 'i', 'WITH', 'i.id = e.individuFk');
      $query = $this->joinIndivSeq($query, 'i', 'seq');
      break;

    case 3: # sequence
      $query = $query->leftJoin('BbeesE3sBundle:SequenceAssemblee', 'seq', 'WITH', 'seq.id=e.sequenceAssembleeFk')
        ->leftJoin('BbeesE3sBundle:SequenceAssembleeExt', 'seqext', 'WITH', 'seqext.id=e.sequenceAssembleeExtFk')
        ->leftJoin('BbeesE3sBundle:EstAligneEtTraite', 'eaet', 'WITH', 'eaet.sequenceAssembleeFk = seq.id')
        ->leftJoin('BbeesE3sBundle:Chromatogramme', 'chromatogramme', 'WITH', 'eaet.chromatogrammeFk = chromatogramme.id')
        ->leftJoin('BbeesE3sBundle:Pcr', 'pcr', 'WITH', 'chromatogramme.pcrFk = pcr.id')
        ->join('BbeesE3sBundle:Assigne', 'ass', 'WITH', 'ass.sequenceAssembleeExtFk=seqext.id OR ass.sequenceAssembleeFk=seq.id')
        ->join('BbeesE3sBundle:Voc', 'vocGene', 'WITH', 'vocGene.id=seqext.geneVocFk OR vocGene.id=pcr.geneVocFk')
        ->addSelect('seqext.id as id_ext, seqext.codeSqcAssExt as codeExt, seqext.accessionNumberSqcAssExt as acc_ext');
      break;
    }

    $query = $query->join('BbeesE3sBundle:Motu', 'm', 'WITH', 'ass.motuFk = m.id')
      ->join('BbeesE3sBundle:Voc', 'voc', 'WITH', 'ass.methodeMotuVocFk = voc.id')
      ->andWhere('rt.id = :id_taxon')
      ->andWhere('voc.id = :methode')
      ->andWhere('m.id = :date_motu')
      ->setParameters([
        'id_taxon'  => $id_taxon,
        'methode'   => $id_methode,
        'date_motu' => $date_methode,
      ]);

    if ($criteres) {
      $query = $query->andWhere('e.critereIdentificationVocFk IN (:criteres)')
        ->setParameter('criteres', $criteres);
    }

    $query = $query->distinct()->getQuery();

    $res = $query->getArrayResult();

    # fusion des résultats séquences internes/externes
    foreach ($res as $key => $row) {
      $res[$key]['type'] = ($row['id']) ? 0 : 1;
      $res[$key]['id']   = ($row['id']) ? $row['id'] : $row['id_ext'];
      $res[$key]['acc']  = ($row['acc']) ? $row['acc'] : $row['acc_ext'];
      $res[$key]['code'] = ($row['code']) ? $row['code'] : $row['codeExt'];
    }
    return $res;
  }

  public function getSpeciesGeoDetails($id, $co1 = -1) {

    if ($co1) {
      $station_subquery = "SELECT DISTINCT
            eid.referentiel_taxon_fk,
            sta.id as id_sta, sta.long_deg_dec as longitude, sta.lat_deg_dec as latitude
            FROM ESPECE_IDENTIFIEE eid
            LEFT JOIN sequence_assemblee_ext sext ON eid.sequence_assemblee_ext_fk=sext.id
            LEFT JOIN voc v1 ON v1.id=sext.gene_voc_fk
            LEFT JOIN sequence_assemblee seq ON eid.sequence_assemblee_fk=seq.id
            LEFT JOIN est_aligne_et_traite eat ON eat.sequence_assemblee_fk=seq.id
            LEFT JOIN chromatogramme chr ON chr.id = eat.chromatogramme_fk
            LEFT JOIN pcr ON chr.pcr_fk=pcr.id
            LEFT JOIN voc v2 ON pcr.gene_voc_fk=v2.id
            LEFT JOIN voc statut ON statut.id=seq.statut_sqc_ass_voc_fk
            LEFT JOIN adn ON pcr.adn_fk=adn.id
            LEFT JOIN individu ind ON ind.id = adn.individu_fk
            LEFT JOIN lot_materiel lm ON ind.lot_materiel_fk=lm.id
            JOIN collecte co ON co.id = sext.collecte_fk OR co.id=lm.collecte_fk
            JOIN station sta ON co.station_fk = sta.id
            WHERE v1.code='COI' OR v2.code='COI'
            AND statut.code IN ('SHORT', 'VALIDEE')";
    } else {
      $station_subquery = "SELECT DISTINCT
             eid.referentiel_taxon_fk,
             sta.id as id_sta, sta.long_deg_dec as longitude, sta.lat_deg_dec as latitude
            FROM ESPECE_IDENTIFIEE eid
            LEFT JOIN lot_materiel lm ON eid.lot_materiel_fk=lm.id
            LEFT JOIN lot_materiel_ext lmext ON eid.lot_materiel_ext_fk=lmext.id
            JOIN collecte co ON co.id = lm.collecte_fk OR co.id=lmext.collecte_fk
            JOIN station sta ON co.station_fk = sta.id";
    }

    $rawSql = "WITH esta AS ($station_subquery)";
    $rawSql .= "SELECT DISTINCT
                rt.id as taxon_id,
                rt.taxname,
                s.id,
                s.code_station,
                s.lat_deg_dec as latitude,
                s.long_deg_dec as longitude,
                s.altitude_m as altitude,
                c.nom_commune as commune,
                p.nom_pays as pays
            FROM referentiel_taxon rt
            JOIN esta ON esta.referentiel_taxon_fk = rt.id
            JOIN station s ON s.id = esta.id_sta
            LEFT JOIN commune c ON c.id=s.commune_fk
            LEFT JOIN pays p ON s.pays_fk=p.id
            WHERE rt.id=:id";

    $stmt = $this->entityManager->getConnection()->prepare($rawSql);
    $stmt->execute(array(
      'id' => $id,
    ));

    return $stmt->fetchAll();
  }

  public function getSpeciesGeoSummary($data, $coi = false) {

    $FIELD_SUFFIX = $coi ? "_co1" : "";

    if ($coi) {
      $station_subquery = "SELECT DISTINCT
            eid.referentiel_taxon_fk,
            sta.id as id_sta, sta.long_deg_dec as longitude, sta.lat_deg_dec as latitude
            FROM ESPECE_IDENTIFIEE eid
            LEFT JOIN sequence_assemblee_ext sext ON eid.sequence_assemblee_ext_fk=sext.id
            LEFT JOIN voc v1 ON v1.id=sext.gene_voc_fk
            LEFT JOIN sequence_assemblee seq ON eid.sequence_assemblee_fk=seq.id
            LEFT JOIN est_aligne_et_traite eat ON eat.sequence_assemblee_fk=seq.id
            LEFT JOIN chromatogramme chr ON chr.id = eat.chromatogramme_fk
            LEFT JOIN pcr ON chr.pcr_fk=pcr.id
            LEFT JOIN voc v2 ON pcr.gene_voc_fk=v2.id
            LEFT JOIN voc statut ON statut.id=seq.statut_sqc_ass_voc_fk
            LEFT JOIN adn ON pcr.adn_fk=adn.id
            LEFT JOIN individu ind ON ind.id = adn.individu_fk
            LEFT JOIN lot_materiel lm ON ind.lot_materiel_fk=lm.id
            JOIN collecte co ON co.id = sext.collecte_fk OR co.id=lm.collecte_fk
            JOIN station sta ON co.station_fk = sta.id
            WHERE v1.code='COI' OR v2.code='COI'
            AND statut.code IN ('SHORT', 'VALIDEE')";
    } else {
      $station_subquery = "SELECT DISTINCT
             eid.referentiel_taxon_fk,
             sta.id as id_sta, sta.long_deg_dec as longitude, sta.lat_deg_dec as latitude
            FROM ESPECE_IDENTIFIEE eid
            LEFT JOIN lot_materiel lm ON eid.lot_materiel_fk=lm.id
            LEFT JOIN lot_materiel_ext lmext ON eid.lot_materiel_ext_fk=lmext.id
            JOIN collecte co ON co.id = lm.collecte_fk OR co.id=lmext.collecte_fk
            JOIN station sta ON co.station_fk = sta.id";
    }

    $rawSql = "WITH esta AS ($station_subquery)";

    $rawSql .= " SELECT
            main.id as arrkey,
            main.id,
            main.taxname,
            nb_sta as nb_sta$FIELD_SUFFIX,
            LMP as LMP$FIELD_SUFFIX,
            dist.MLE as MLE$FIELD_SUFFIX
            FROM (
            SELECT
                rt.taxname,
                rt.id,
                COUNT(distinct esta.id_sta) as nb_sta,
                (min(esta.latitude) + (max(esta.latitude)-min(esta.latitude))/2)  as LMP
                FROM referentiel_taxon rt
                JOIN espece_identifiee e ON e.referentiel_taxon_fk = rt.id
                JOIN esta ON esta.referentiel_taxon_fk=rt.id
                JOIN voc ON voc.id = e.critere_identification_voc_fk";

    if ($data->get('taxaFilter')) {
      $rawSql .= " AND rt.genus=:genus AND rt.species=:species";
    }

    $rawSql .= " GROUP BY rt.id, rt.taxname
        ORDER BY nb_sta DESC
        ) main
        LEFT JOIN (
            SELECT
                sta1.referentiel_taxon_fk,
                max((point(sta1.longitude,sta1.latitude) <@> point(sta2.longitude, sta2.latitude)) * 1.609344) as MLE
            FROM esta sta1
            JOIN esta sta2 ON sta1.referentiel_taxon_fk = sta2.referentiel_taxon_fk
            WHERE sta1.id_sta < sta2.id_sta
            GROUP BY sta1.referentiel_taxon_fk
        ) dist ON dist.referentiel_taxon_fk=main.id
        ORDER BY taxname;";

    $stmt = $this->entityManager->getConnection()->prepare($rawSql);
    if ($data->get('taxaFilter')) {
      $stmt->execute(array(
        'genus'   => $data->get('genus'),
        'species' => $data->get('species'),
      ));
    } else {
      $stmt->execute();
    }
    return $stmt->fetchAll(\PDO::FETCH_UNIQUE | \PDO::FETCH_ASSOC);
  }

  public function getMotuGeoLocation($data, $single_method = false) {

    $taxid = $data->get('taxname');

    if ($data->get('methode') && $single_method) {
      $methodFields = "motu.motu as motu";
    } else {
      $methods = $this->listMethodsByDate();
      //dump($methods);
      $methodFields = [];
      foreach ($methods as $idm => $method) {
        $methods[$idm]['tName'] = $method['code'] . "_" . $method['id_date_motu'];
        $methodFields[]         = $methods[$idm]['tName'] . ".motu as " . $method['code'] . "_" . $method['date_motu']->format('Y');
      }
      $methodFields = join(',', $methodFields);
    }

    $subquery = "SELECT
            seq.id, type_seq,
            voc.id as id_methode, voc.code as methode,
            motu.id as id_date_motu, motu.date_motu, num_motu as motu
        FROM (
            SELECT sequence_assemblee.id as id,
                    0 as type_seq,
                    assigne.methode_motu_voc_fk as methode_voc,
                    assigne.num_motu,
                    assigne.motu_fk
            FROM assigne JOIN sequence_assemblee ON assigne.sequence_assemblee_fk=sequence_assemblee.id
            UNION
            SELECT sequence_assemblee_ext.id as id,
                    1 as type_seq,
                    assigne.methode_motu_voc_fk as methode_voc,
                    assigne.num_motu,
                    assigne.motu_fk
            FROM assigne JOIN sequence_assemblee_ext ON assigne.sequence_assemblee_ext_fk=sequence_assemblee_ext.id
            ) as seq
            JOIN voc ON voc.id=seq.methode_voc
            JOIN motu ON motu.id=seq.motu_fk";

    $rawSql = "WITH liste_motus AS ($subquery)";
    $rawSql .= "SELECT DISTINCT seq.id, seq.code, seq.accession_number,
            seq.delimitation,
            seq.type_seq,
            $methodFields,
            tax.id as taxon_id,
            tax.taxname,
            station.id as id_sta,
            station.altitude_m as altitude,
            station.lat_deg_dec as latitude,
            station.long_deg_dec as longitude,
            station.code_station,
            commune.nom_commune as commune,
            pays.nom_pays as pays

            FROM (SELECT id,code,  accession_number,
                 collecte_fk, rt, delimitation, type_seq
                FROM (
                    SELECT  sequence_assemblee_ext.id,
                            sequence_assemblee_ext.code_sqc_ass_ext as code,
                            accession_number_sqc_ass_ext as accession_number,
                            collecte_fk, ei.referentiel_taxon_fk as rt,
                            critere.code as delimitation,
                            1 as type_seq
                    FROM sequence_assemblee_ext
                        LEFT JOIN espece_identifiee ei on ei.sequence_assemblee_ext_fk=sequence_assemblee_ext.id
                        LEFT JOIN voc critere ON ei.critere_identification_voc_fk=critere.id
                UNION
                    SELECT  seqas.id,
                            seqas.code_sqc_ass as code,
                            seqas.accession_number,
                            lmat.collecte_fk, ei.referentiel_taxon_fk as rt,
                            critere.code as delimitation,
                            0 as type_seq
                    FROM lot_materiel lmat
                        JOIN individu I ON I.lot_materiel_fk=lmat.id
                        JOIN adn ON adn.individu_fk=I.id
                        JOIN pcr ON pcr.adn_fk=adn.id
                        JOIN chromatogramme ON chromatogramme.pcr_fk=pcr.id
                        JOIN est_aligne_et_traite eaet ON chromatogramme.id=eaet.chromatogramme_fk
                        JOIN sequence_assemblee seqas ON seqas.id=eaet.sequence_assemblee_fk
                        LEFT JOIN espece_identifiee ei on ei.sequence_assemblee_fk=seqas.id
                        LEFT JOIN voc critere ON ei.critere_identification_voc_fk=critere.id
                    ) AS union_seq ) AS seq
            LEFT JOIN referentiel_taxon tax ON tax.id=seq.rt
            JOIN collecte ON seq.collecte_fk=collecte.id
            JOIN station ON collecte.station_fk=station.id
            JOIN commune ON station.commune_fk=commune.id
            JOIN pays ON station.pays_fk=pays.id";

    if ($single_method) {
      $rawSql .= " JOIN (SELECT * FROM liste_motus
                    WHERE id_date_motu = :id_date_motu AND id_methode=:id_methode ) motu
                    ON seq.id=motu.id and motu.type_seq = seq.type_seq";
    } else {
      foreach ($methods as $idm => $method) {
        $rawSql .= " LEFT JOIN (	SELECT * FROM liste_motus
                        WHERE id_date_motu=" . $method['id_date_motu'] . " AND id_methode='" . $method['id'] . "') " . $method['tName'] .
          " ON seq.id=" . $method['tName'] . ".id AND " . $method['tName'] . ".type_seq = seq.type_seq";
      }
    }
    if ($taxid) {
      $rawSql .= " WHERE tax.id = :taxid";
    }

    $rawSql .= " ORDER BY taxname";

    $stmt = $this->entityManager->getConnection()->prepare($rawSql);
    if ($taxid) {
      $stmt->bindParam('taxid', $taxid);
    };

    if ($single_method) {
      $stmt->bindValue('id_date_motu', $data->get('date_methode'));
      $stmt->bindValue('id_methode', $data->get('methode'));
    }

    $stmt->execute();
    return $stmt->fetchAll();
  }
}
