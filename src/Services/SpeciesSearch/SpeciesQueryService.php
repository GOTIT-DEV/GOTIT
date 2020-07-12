<?php

namespace App\Services\SpeciesSearch;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Service SpeciesQueryService
 */
class SpeciesQueryService
{
  private $entityManager;

  public function __construct(EntityManagerInterface $manager)
  {
    $this->entityManager = $manager;
  }

  /***************************************************************************
   * UTILITY QUERIES
   ***************************************************************************/

  public function getGenusSet()
  {
    $qb    = $this->entityManager->createQueryBuilder();
    $query = $qb->select('rt.genus')
      ->from('App:ReferentielTaxon', 'rt')
      ->where('rt.genus IS NOT NULL')
      ->distinct()
      ->orderBy('rt.genus')
      ->getQuery();
    return $query->getResult();
  }

  public function getMethodsByDate($id_dataset)
  {
    $qb    = $this->entityManager->createQueryBuilder();
    $query = $qb->select('v.id, v.code, m.id as id_dataset, m.libelleMotu as motu_title')
      ->from('App:Motu', 'm')
      ->join('App:Assigne', 'a', 'WITH', 'a.motuFk=m')
      ->join('App:Voc', 'v', 'WITH', "a.methodeMotuVocFk=v AND v.code != 'HAPLO'")
      ->andWhere('m.id = :dataset')
      ->setParameter('dataset', $id_dataset)
      ->distinct()
      ->getQuery();

    return $query->getArrayResult();
  }

  public function getMethod($id_methode, $id_dataset)
  {
    $qb    = $this->entityManager->createQueryBuilder();
    $query = $qb->select('v.id as id_methode, v.code')
      ->addSelect('m.id as id_dataset, m.dateMotu as date_dataset, m.libelleMotu as motu_title')
      ->from('App:Motu', 'm')
      ->join('App:Assigne', 'a', 'WITH', 'a.motuFk=m')
      ->join('App:Voc', 'v', 'WITH', "a.methodeMotuVocFk=v AND v.code != 'HAPLO'")
      ->andWhere('m.id = :id_dataset AND v.id = :id_methode')
      ->setParameters(array(
        ':id_dataset' => $id_dataset,
        ':id_methode' => $id_methode,
      ))
      ->distinct()
      ->getQuery();

    return $query->getArrayResult();
  }

  public function listMethodsByDate()
  {
    $qb    = $this->entityManager->createQueryBuilder();
    $query = $qb->select('v.id, v.code, m.id as id_dataset, m.dateMotu as date_dataset, m.libelleMotu as motu_title')
      ->from('App:Motu', 'm')
      ->join('App:Assigne', 'a', 'WITH', 'a.motuFk=m')
      ->join('App:Voc', 'v', 'WITH', "a.methodeMotuVocFk=v AND v.code != 'HAPLO'")
      ->distinct()
      ->orderBy('m.id, v.id')
      ->getQuery();

    return $query->getArrayResult();
  }

  /****************************************************************************
   * JOINERS
   ****************************************************************************/

  private function joinIndivSeq($query, $indivAlias, $seqAlias)
  {
    return $query->join('App:Adn', 'dna', 'WITH', "$indivAlias.id = dna.individuFk")
      ->join('App:Pcr', 'pcr', 'WITH', 'dna.id = pcr.adnFk')
      ->join('App:Chromatogramme', 'ch', 'WITH', 'pcr.id = ch.pcrFk')
      ->join('App:EstAligneEtTraite', 'at', 'WITH', 'at.chromatogrammeFk = ch.id')
      ->join('App:Assigne', 'ass', 'WITH', 'ass.sequenceAssembleeFk = at.sequenceAssembleeFk')
      ->join('App:SequenceAssemblee', $seqAlias, 'WITH', "$seqAlias.id = at.sequenceAssembleeFk")
      ->join('App:Voc', 'vocGene', 'WITH', 'vocGene.id = pcr.geneVocFk');
  }

  private function leftJoinIndivSeq($query, $indivAlias, $seqAlias)
  {
    return $query->leftJoin('App:Adn', 'dna', 'WITH', "$indivAlias.id = dna.individuFk")
      ->leftJoin('App:Pcr', 'pcr', 'WITH', 'dna.id = pcr.adnFk')
      ->leftJoin('App:Chromatogramme', 'ch', 'WITH', 'pcr.id = ch.pcrFk')
      ->leftJoin('App:EstAligneEtTraite', 'at', 'WITH', 'at.chromatogrammeFk = ch.id')
      ->leftJoin('App:Assigne', 'ass', 'WITH', 'ass.sequenceAssembleeFk = at.sequenceAssembleeFk')
      ->leftJoin('App:SequenceAssemblee', $seqAlias, 'WITH', "$seqAlias.id = at.sequenceAssembleeFk")
      ->leftJoin('App:Voc', 'vocGene', 'WITH', 'vocGene.id = pcr.geneVocFk');
  }

  private function joinEspeceStation($query, $aliasEsp, $aliasSta)
  {
    return $query->leftJoin('App:LotMateriel', 'lm', 'WITH', $aliasEsp . '.lotMaterielFk=lm.id')
      ->leftJoin('App:LotMaterielExt', 'lmext', 'WITH', $aliasEsp . '.lotMaterielExtFk=lmext.id')
      ->join('App:Collecte', 'c', 'WITH', 'c.id=lm.collecteFk OR c.id=lmext.collecteFk')
      ->join('App:Station', $aliasSta, 'WITH', $aliasSta . '.id=c.stationFk');
  }

  private function joinMotuCountMorpho($query, $alias = 'ass')
  {
    return $query->leftJoin('App:SequenceAssembleeExt', 'motu_sext', 'WITH', "motu_sext.id=$alias.sequenceAssembleExtFk")
      ->leftJoin('App:EstAligneEtTraite', 'motu_at', 'WITH', "motu_at.sequenceAssembleeFk = $alias.sequenceAssembleeFk")
      ->leftJoin('App:Chromatogramme', 'motu_chr', 'WITH', "motu_chr.id = motu_at.chromatogrammeFk")
      ->leftJoin('App:Pcr', 'motu_pcr', 'WITH', "motu_pcr.id = motu_chr.pcrFk")
      ->leftJoin('App:Adn', 'motu_adn', 'WITH', "motu_adn.id = motu_pcr.adnFk")
      ->leftJoin('App:Individu', 'motu_ind', 'WITH', "motu_ind.id = motu_adn.individuFk")
      ->join('App:EspeceIdentifiée', 'motu_eid', 'WITH', "motu_eid.individuFk = motu_ind.id OR motu_eid.sequenceAssembleeExtFk=motu_sext.id")
      ->join('App:Voc', 'motu_voc', 'WITH', "motu_voc.id = $alias.methodeMotuVocFk")
      ->join('App:Motu', 'motu_date', 'WITH', "motu_date.id = $alias.motuFk");
  }

  /*****************************************************************************
   * QUERIES
   *****************************************************************************/
  public function getMotuCountList($data)
  {

    $niveau   = $data->get('niveau');
    $methodes = $data->get('methodes');
    $dataset  = $data->get('dataset');
    $criteres = $data->get('criteres');

    $qb    = $this->entityManager->createQueryBuilder();
    $query = $qb->select('rt.taxname, rt.id')
      ->addSelect('vocabulary.id as id_methode, vocabulary.code as methode')
      ->addSelect('motu.id as id_dataset, motu.dateMotu as motu_date, motu.libelleMotu as motu_title')
      ->addSelect('COUNT(DISTINCT ass.numMotu ) as nb_motus')
      ->from('App:ReferentielTaxon', 'rt')
      ->join('App:EspeceIdentifiee', 'e', 'WITH', 'rt.id = e.referentielTaxonFk');
    switch ($niveau) {
      case 1: #lot matériel
        $query = $query->join('App:LotMateriel', 'lm', 'WITH', 'lm.id=e.lotMaterielFk')
          ->join('App:Individu', 'i', 'WITH', 'i.lotMaterielFk = lm.id');
        $query = $this->joinIndivSeq($query, 'i', 'seq')->addSelect('COUNT(DISTINCT seq.id) as nb_seq');
        break;

      case 2: #individu
        $query = $query->join('App:Individu', 'i', 'WITH', 'i.id = e.individuFk');
        $query = $this->joinIndivSeq($query, 'i', 'seq')->addSelect('COUNT(DISTINCT seq.id) as nb_seq');
        break;

      case 3: # sequence
        $query = $query->leftJoin('App:SequenceAssemblee', 'seq', 'WITH', 'seq.id=e.sequenceAssembleeFk')
          ->leftJoin('App:SequenceAssembleeExt', 'seqext', 'WITH', 'seqext.id=e.sequenceAssembleeExtFk')
          ->join('App:Assigne', 'ass', 'WITH', 'ass.sequenceAssembleeExtFk=seqext.id OR ass.sequenceAssembleeFk=seq.id')
          ->addSelect('(COUNT(DISTINCT seq.id) + COUNT(DISTINCT seqext.id)) as nb_seq');
        break;
    }

    $query = $query->join('App:Motu', 'motu', 'WITH', 'ass.motuFk = motu.id')
      ->join('App:Voc', 'vocabulary', 'WITH', 'ass.methodeMotuVocFk = vocabulary.id');

    if ($data->get('species')) {
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

    $query = $query->andWHere("vocabulary.code != 'HAPLO'")
      ->andWhere('motu.id = :id_dataset')
      ->setParameter('id_dataset', $dataset)
      ->groupBy('rt.id, rt.taxname, vocabulary.id, vocabulary.code, motu.id')
      ->orderBy('rt.id')
      ->getQuery();

    return $query->getArrayResult();
  }

  public function getSpeciesAssignment($data)
  {
    $columnsMap = array(
      'lot-materiel' => 'lmrt',
      'individu'     => 'indivrt',
      'sequence'     => 'seqrt',
    );
    $typeConstraints = array_fill_keys(
      ["A", "B", "C"],
      array()
    );
    $undefinedSeq = ($data->get('sequence') == '1');
    foreach ($data as $key => $value) {
      if (in_array($value, array_keys($typeConstraints))) {
        $typeConstraints[$value][] = $columnsMap[$key];
      }
    }

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
      ->from('App:LotMateriel', 'lm')
      ->join('App:EspeceIdentifiee', 'eidlm', 'WITH', 'lm.id = eidlm.lotMaterielFk')
      ->join('App:ReferentielTaxon', 'lmrt', 'WITH', 'lmrt.id = eidlm.referentielTaxonFk')
      ->join('App:Voc', 'lmvoc', 'WITH', 'eidlm.critereIdentificationVocFk=lmvoc.id')
      // JOIN individu
      ->join('App:Individu', 'indiv', 'WITH', 'indiv.lotMaterielFk = lm.id')
      ->join('App:EspeceIdentifiee', 'eidindiv', 'WITH', 'indiv.id = eidindiv.individuFk')
      ->join('App:ReferentielTaxon', 'indivrt', 'WITH', 'indivrt.id = eidindiv.referentielTaxonFk')
      ->join('App:Voc', 'ivoc', 'WITH', 'eidindiv.critereIdentificationVocFk=ivoc.id');
    // JOIN sequence
    $query = $this->leftJoinIndivSeq($query, 'indiv', 'seq')
      ->leftJoin('App:EspeceIdentifiee', 'eidseq', 'WITH', 'seq.id = eidseq.sequenceAssembleeFk')
      ->leftJoin('App:ReferentielTaxon', 'seqrt', 'WITH', 'seqrt.id = eidseq.referentielTaxonFk')
      ->leftJoin('App:Voc', 'seqvoc', 'WITH', 'eidseq.critereIdentificationVocFk=seqvoc.id');
    if ($undefinedSeq) {
      $query = $query->andWhere('seq.id IS NULL');
    }
    // FILTER based on user defined constraints
    $visited = [];
    foreach ($typeConstraints as $type => $identicals) {
      if ($identicals) {
        $current  = [];
        $refTable = $identicals[0];
        foreach ($identicals as $tableAlias) {
          $current[] = $tableAlias;
          if ($tableAlias != $refTable) {
            $query = $query->andWhere("$refTable.id = $tableAlias.id");
          }
          foreach ($visited as $different) {
            $query = $query->andWhere("$tableAlias.id != $different.id");
          }
        }
        $visited = array_merge($current, $visited);
      }
    }
    $query = $query->distinct()->getQuery();
    $res   = $query->getArrayResult();
    return $res;
  }

  public function getMotuSeqList($data)
  {
    $id_taxon   = $data->get('taxon');
    $id_methode = $data->get('methode');
    $dataset    = $data->get('date_motu');
    $niveau     = $data->get('niveau');
    $criteres   = $data->get('criteres');

    $qb    = $this->entityManager->createQueryBuilder();
    $query = $qb->select('rt.id as idesp, rt.taxname')
      ->addSelect('vocabulary.code as methode')
      ->addSelect('m.dateMotu as motu_date')
      ->addSelect('seq.id, seq.codeSqcAss as code, seq.accessionNumber as acc')
      ->addSelect('ass.numMotu as motu')
      ->addSelect('v.code as critere')
      ->addSelect('vocGene.code as gene')
      ->from('App:ReferentielTaxon', 'rt')
      ->join('App:EspeceIdentifiee', 'e', 'WITH', 'rt.id = e.referentielTaxonFk')
      ->join('App:Voc', 'v', 'WITH', 'e.critereIdentificationVocFk=v.id');
    switch ($niveau) {
      case 1: # Bio material
        $query = $query->join('App:LotMateriel', 'lm', 'WITH', 'lm.id=e.lotMaterielFk')
          ->join('App:Individu', 'i', 'WITH', 'i.lotMaterielFk = lm.id');
        $query = $this->joinIndivSeq($query, 'i', 'seq');
        break;

      case 2: # Specimen
        $query = $query->join('App:Individu', 'i', 'WITH', 'i.id = e.individuFk');
        $query = $this->joinIndivSeq($query, 'i', 'seq');
        break;

      case 3: # Sequence
        $query = $query->leftJoin('App:SequenceAssemblee', 'seq', 'WITH', 'seq.id=e.sequenceAssembleeFk')
          ->leftJoin('App:SequenceAssembleeExt', 'seqext', 'WITH', 'seqext.id=e.sequenceAssembleeExtFk')
          ->leftJoin('App:EstAligneEtTraite', 'chrom_proc', 'WITH', 'chrom_proc.sequenceAssembleeFk = seq.id')
          ->leftJoin('App:Chromatogramme', 'chromatogram', 'WITH', 'chrom_proc.chromatogrammeFk = chromatogram.id')
          ->leftJoin('App:Pcr', 'pcr', 'WITH', 'chromatogram.pcrFk = pcr.id')
          ->join('App:Assigne', 'ass', 'WITH', 'ass.sequenceAssembleeExtFk=seqext.id OR ass.sequenceAssembleeFk=seq.id')
          ->join('App:Voc', 'vocGene', 'WITH', 'vocGene.id=seqext.geneVocFk OR vocGene.id=pcr.geneVocFk')
          ->addSelect('seqext.id as id_ext, seqext.codeSqcAssExt as codeExt, seqext.accessionNumberSqcAssExt as acc_ext');
        break;
    }

    $query = $query->join('App:Motu', 'm', 'WITH', 'ass.motuFk = m.id')
      ->join('App:Voc', 'vocabulary', 'WITH', 'ass.methodeMotuVocFk = vocabulary.id')
      ->andWhere('rt.id = :id_taxon')
      ->andWhere('vocabulary.id = :method')
      ->andWhere('m.id = :dataset')
      ->setParameters([
        'id_taxon'  => $id_taxon,
        'method'   => $id_methode,
        'dataset' => $dataset,
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

  public function getSpeciesGeoDetails($id, $co1 = false)
  {

    if ($co1) {
      $station_subquery = "SELECT DISTINCT
            eid.taxon_fk, lm.id as lm_id,
            sta.id as id_sta, sta.longitude as longitude, sta.latitude as latitude
            FROM identified_species eid
            LEFT JOIN external_sequence sext ON eid.external_sequence_fk=sext.id
            LEFT JOIN vocabulary v1 ON v1.id=sext.gene_voc_fk
            LEFT JOIN internal_sequence seq ON eid.internal_sequence_fk=seq.id
            LEFT JOIN chromatogram_is_processed_to eat ON eat.internal_sequence_fk=seq.id
            LEFT JOIN chromatogram chr ON chr.id = eat.chromatogram_fk
            LEFT JOIN pcr ON chr.pcr_fk=pcr.id
            LEFT JOIN vocabulary v2 ON pcr.gene_voc_fk=v2.id
            LEFT JOIN vocabulary statut ON statut.id=seq.internal_sequence_status_voc_fk
            LEFT JOIN dna ON pcr.dna_fk=dna.id
            LEFT JOIN specimen ind ON ind.id = dna.specimen_fk
            LEFT JOIN internal_biological_material lm ON ind.internal_biological_material_fk=lm.id
            JOIN sampling co ON co.id = sext.sampling_fk OR co.id=lm.sampling_fk
            JOIN site sta ON co.site_fk = sta.id
            WHERE v1.code='COI' OR v2.code='COI'
            AND (
              statut.code = 'SHORT' OR 
              statut.code LIKE 'VALID%'
            )";
    } else {
      $station_subquery = "SELECT DISTINCT
             eid.taxon_fk, lm.id as lm_id,
             sta.id as id_sta, sta.longitude as longitude, sta.latitude as latitude
            FROM identified_species eid
            LEFT JOIN internal_biological_material lm ON eid.internal_biological_material_fk=lm.id
            LEFT JOIN external_biological_material lmext ON eid.external_biological_material_fk=lmext.id
            JOIN sampling co ON co.id = lm.sampling_fk OR co.id=lmext.sampling_fk
            JOIN site sta ON co.site_fk = sta.id";
    }

    $rawSql = "WITH esta AS ($station_subquery)";
    $rawSql .= "SELECT DISTINCT
                rt.id as taxon_id,
                rt.taxon_name as taxon_name,
                esta.lm_id as bio_mat_id,
                s.id as station_id,
                s.site_code as station_code,
                s.latitude as latitude,
                s.longitude as longitude,
                s.elevation as altitude,
                c.municipality_name as municipality,
                p.country_name as country
            FROM taxon rt
            JOIN esta ON esta.taxon_fk = rt.id
            JOIN site s ON s.id = esta.id_sta
            LEFT JOIN municipality c ON c.id=s.municipality_fk
            LEFT JOIN country p ON s.country_fk=p.id
            WHERE rt.id=:id";

    $stmt = $this->entityManager->getConnection()->prepare($rawSql);
    $stmt->execute(array(
      'id' => $id,
    ));

    return $stmt->fetchAll();
  }

  public function getSpeciesGeoSummary($data, $co1 = false)
  {

    // Prepare subquery to list stations depending on CO1 sampling events requested (or not)
    $FIELD_SUFFIX = $co1 ? "_co1" : "";

    if ($co1) {
      $station_subquery = "SELECT DISTINCT
            eid.taxon_fk,
            sta.id as id_sta, sta.longitude as longitude, sta.latitude as latitude
            FROM identified_species eid
            -- External sequences
            LEFT JOIN external_sequence sext ON eid.external_sequence_fk=sext.id
            LEFT JOIN vocabulary v1 ON v1.id=sext.gene_voc_fk
            -- Internal sequences
            LEFT JOIN internal_sequence seq ON eid.internal_sequence_fk=seq.id
            LEFT JOIN chromatogram_is_processed_to eat ON eat.internal_sequence_fk=seq.id
            LEFT JOIN chromatogram chr ON chr.id = eat.chromatogram_fk
            LEFT JOIN pcr ON chr.pcr_fk=pcr.id
            LEFT JOIN vocabulary v2 ON pcr.gene_voc_fk=v2.id
            LEFT JOIN vocabulary statut ON seq.internal_sequence_status_voc_fk=statut.id
            LEFT JOIN dna ON pcr.dna_fk=dna.id
            LEFT JOIN specimen ind ON ind.id = dna.specimen_fk
            LEFT JOIN internal_biological_material lm ON ind.internal_biological_material_fk=lm.id
            -- Find all sampling events ('sampling') of internal or external seq
            JOIN sampling co ON co.id = sext.sampling_fk OR co.id=lm.sampling_fk
            -- Join to corresponding site
            JOIN site sta ON co.site_fk = sta.id
            -- COI constraint
            WHERE v1.code='COI' OR v2.code='COI'
            AND (
              statut.code = 'SHORT' OR 
              statut.code LIKE 'VALID%'
            )";
    } else {
      $station_subquery = "SELECT DISTINCT
             eid.taxon_fk,
             sta.id as id_sta, sta.longitude as longitude, sta.latitude as latitude
            FROM identified_species eid
            LEFT JOIN internal_biological_material lm ON eid.internal_biological_material_fk=lm.id
            LEFT JOIN external_biological_material lmext ON eid.external_biological_material_fk=lmext.id
            JOIN sampling co ON co.id = lm.sampling_fk OR co.id=lmext.sampling_fk
            JOIN site sta ON co.site_fk = sta.id";
    }

    // Compute maximum longitudinal extent (MLE) for each requested species
    $mle_subquery = " SELECT sta1.taxon_fk,
            max((point(sta1.longitude,sta1.latitude) <@> point(sta2.longitude, sta2.latitude)) * 1.609344) as MLE
            FROM esta sta1
            JOIN esta sta2 ON sta1.taxon_fk = sta2.taxon_fk
            WHERE sta1.id_sta < sta2.id_sta
            GROUP BY sta1.taxon_fk";

    $main_subquery = "SELECT
            rt.taxon_name,
            rt.id,
            COUNT(distinct esta.id_sta) as nb_sta,
            (min(esta.latitude) + (max(esta.latitude)-min(esta.latitude))/2)  as LMP
            FROM taxon rt
            JOIN identified_species e ON e.taxon_fk = rt.id
            JOIN esta ON esta.taxon_fk=rt.id
            JOIN vocabulary ON vocabulary.id = e.identification_criterion_voc_fk
            --taxafilter.placeholder
            GROUP BY rt.id, rt.taxon_name
            ORDER BY nb_sta DESC";
    if ($data->get('species')) {
      // Additional filter to query only for a target species
      $main_subquery = str_replace(
        '--taxafilter.placeholder',
        " AND rt.genus=:genus AND rt.species=:species",
        $main_subquery
      );
    }

    $rawSql = "WITH esta AS ($station_subquery), 
                    mle AS ($mle_subquery),
                    main AS ($main_subquery)";
    $rawSql .= " SELECT
            main.id as arrkey,
            main.id,
            main.taxon_name,
            nb_sta as nb_sta$FIELD_SUFFIX,
            LMP as LMP$FIELD_SUFFIX,
            mle.MLE as MLE$FIELD_SUFFIX
            FROM main
            LEFT JOIN mle ON mle.taxon_fk=main.id
            ORDER BY taxon_name;";

    $stmt = $this->entityManager->getConnection()->prepare($rawSql);
    if ($data->get('species')) {
      $stmt->execute(array(
        'genus'   => $data->get('genus'),
        'species' => $data->get('species'),
      ));
    } else {
      $stmt->execute();
    }
    return $stmt->fetchAll(\PDO::FETCH_UNIQUE | \PDO::FETCH_ASSOC);
  }

  public function getMotuGeoLocation($data)
  {

    $taxid    = $data->get('taxname');
    $subquery = "SELECT
            seq.id, type_seq,
            vocabulary.id as id_methode, vocabulary.code as methode,
            motu.id as id_dataset, motu.motu_date, motu.motu_title, motu_number as motu
        FROM (
            SELECT internal_sequence.id as id,
                    0 as type_seq,
                    motu_number.delimitation_method_voc_fk as methode_voc,
                    motu_number.motu_number,
                    motu_number.motu_fk
            FROM motu_number JOIN internal_sequence ON motu_number.internal_sequence_fk=internal_sequence.id
            UNION
            SELECT external_sequence.id as id,
                    1 as type_seq,
                    motu_number.delimitation_method_voc_fk as methode_voc,
                    motu_number.motu_number,
                    motu_number.motu_fk
            FROM motu_number JOIN external_sequence ON motu_number.external_sequence_fk=external_sequence.id
            ) as seq
            JOIN vocabulary ON vocabulary.id=seq.methode_voc
            JOIN motu ON motu.id=seq.motu_fk
            WHERE motu.id = :id_dataset AND vocabulary.id=:id_methode ";

    $rawSql = "WITH liste_motus AS ($subquery)";
    $rawSql .= "SELECT DISTINCT seq.id, seq.code, seq.accession_number,
            seq.delimitation,
            seq.type_seq as seq_type,
            liste_motus.id_methode,
            liste_motus.methode,
            liste_motus.id_dataset,
            liste_motus.motu_title,
            liste_motus.motu,
            tax.id as taxon_id,
            tax.taxon_name,
            site.id as id_sta,
            site.elevation as altitude,
            site.latitude as latitude,
            site.longitude as longitude,
            site.site_code as station_code,
            municipality.municipality_name as municipality,
            country.country_name as country

            FROM (SELECT id,code,  accession_number,
                 sampling_fk, rt, delimitation, type_seq
                FROM (
                    SELECT  external_sequence.id,
                            external_sequence.external_sequence_code as code,
                            external_sequence_accession_number as accession_number,
                            sampling_fk, ei.taxon_fk as rt,
                            critere.code as delimitation,
                            1 as type_seq
                    FROM external_sequence
                        LEFT JOIN identified_species ei on ei.external_sequence_fk=external_sequence.id
                        LEFT JOIN vocabulary critere ON ei.identification_criterion_voc_fk=critere.id
                UNION
                    SELECT  seq_int.id,
                            seq_int.internal_sequence_code as code,
                            seq_int.internal_sequence_accession_number as accession_number,
                            lmat.sampling_fk, ei.taxon_fk as rt,
                            critere.code as delimitation,
                            0 as type_seq
                    FROM internal_biological_material lmat
                        JOIN specimen I ON I.internal_biological_material_fk=lmat.id
                        JOIN dna ON dna.specimen_fk=I.id
                        JOIN pcr ON pcr.dna_fk=dna.id
                        JOIN chromatogram ON chromatogram.pcr_fk=pcr.id
                        JOIN chromatogram_is_processed_to chrom_proc ON chromatogram.id=chrom_proc.chromatogram_fk
                        JOIN internal_sequence seq_int ON seq_int.id=chrom_proc.internal_sequence_fk
                        LEFT JOIN identified_species ei on ei.internal_sequence_fk=seq_int.id
                        LEFT JOIN vocabulary critere ON ei.identification_criterion_voc_fk=critere.id
                    ) AS union_seq ) AS seq
            LEFT JOIN taxon tax ON tax.id=seq.rt
            JOIN sampling ON seq.sampling_fk=sampling.id
            JOIN site ON sampling.site_fk=site.id
            JOIN municipality ON site.municipality_fk=municipality.id
            JOIN country ON site.country_fk=country.id
            JOIN liste_motus ON seq.id=liste_motus.id AND liste_motus.type_seq = seq.type_seq";
    if ($taxid) {
      $rawSql .= " WHERE tax.id = :taxid";
    }

    $rawSql .= " ORDER BY taxon_name, seq.code";

    $stmt = $this->entityManager->getConnection()->prepare($rawSql);
    if ($taxid) {
      $stmt->bindParam('taxid', $taxid);
    };

    $stmt->bindValue('id_dataset', $data->get('dataset'));
    $stmt->bindValue('id_methode', $data->get('methods'));

    $stmt->execute();
    return $stmt->fetchAll();
  }
}
