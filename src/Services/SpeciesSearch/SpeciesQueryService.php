<?php

namespace App\Services\SpeciesSearch;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Service SpeciesQueryService
 */
class SpeciesQueryService {
  private $entityManager;

  public function __construct(EntityManagerInterface $manager) {
    $this->entityManager = $manager;
  }

  /***************************************************************************
   * UTILITY QUERIES
   ***************************************************************************/

  public function getGenusSet() {
    $qb = $this->entityManager->createQueryBuilder();
    $query = $qb->select('rt.genus')
      ->from('App:Taxon', 'rt')
      ->where('rt.genus IS NOT NULL')
      ->distinct()
      ->orderBy('rt.genus')
      ->getQuery();
    return $query->getResult();
  }

  public function getMethodsByDate($id_dataset) {
    $qb = $this->entityManager->createQueryBuilder();
    $query = $qb->select('v.id, v.code, m.id as id_dataset, m.title as motu_title')
      ->from('App:MotuDataset', 'm')
      ->join('App:MotuDelimitation', 'a', 'WITH', 'a.motuDatasetFk=m')
      ->join('App:Voc', 'v', 'WITH', "a.methodVocFk=v AND v.code != 'HAPLO'")
      ->andWhere('m.id = :dataset')
      ->setParameter('dataset', $id_dataset)
      ->distinct()
      ->getQuery();

    return $query->getArrayResult();
  }

  public function getMethod($id_methode, $id_dataset) {
    $qb = $this->entityManager->createQueryBuilder();
    $query = $qb->select('v.id as id_methode, v.code')
      ->addSelect('m.id as id_dataset, m.date as date_dataset, m.title as motu_title')
      ->from('App:MotuDataset', 'm')
      ->join('App:MotuDelimitation', 'a', 'WITH', 'a.motuDatasetFk=m')
      ->join('App:Voc', 'v', 'WITH', "a.methodVocFk=v AND v.code != 'HAPLO'")
      ->andWhere('m.id = :id_dataset AND v.id = :id_methode')
      ->setParameters(array(
        ':id_dataset' => $id_dataset,
        ':id_methode' => $id_methode,
      ))
      ->distinct()
      ->getQuery();

    return $query->getArrayResult();
  }

  public function listMethodsByDate() {
    $qb = $this->entityManager->createQueryBuilder();
    $query = $qb->select('v.id, v.code, m.id as id_dataset, m.date as date_dataset, m.title as motu_title')
      ->from('App:MotuDataset', 'm')
      ->join('App:MotuDelimitation', 'a', 'WITH', 'a.motuDatasetFk=m')
      ->join('App:Voc', 'v', 'WITH', "a.methodVocFk=v AND v.code != 'HAPLO'")
      ->distinct()
      ->orderBy('m.id, v.id')
      ->getQuery();

    return $query->getArrayResult();
  }

  /****************************************************************************
   * JOINERS
   ****************************************************************************/

  private function joinIndivSeq($query, $indivAlias, $seqAlias) {
    return $query->join('App:Dna', 'dna', 'WITH', "$indivAlias.id = dna.specimenFk")
      ->join('App:Pcr', 'pcr', 'WITH', 'dna.id = pcr.dnaFk')
      ->join('App:Chromatogram', 'ch', 'WITH', 'pcr.id = ch.pcrFk')
      ->join('App:InternalSequenceAssembly', 'at', 'WITH', 'at.chromatogramFk = ch.id')
      ->join('App:MotuDelimitation', 'ass', 'WITH', 'ass.internalSequenceFk = at.internalSequenceFk')
      ->join('App:InternalSequence', $seqAlias, 'WITH', "$seqAlias.id = at.internalSequenceFk")
      ->join('App:Voc', 'vocGene', 'WITH', 'vocGene.id = pcr.geneVocFk');
  }

  private function leftJoinIndivSeq($query, $indivAlias, $seqAlias) {
    return $query->leftJoin('App:Dna', 'dna', 'WITH', "$indivAlias.id = dna.specimenFk")
      ->leftJoin('App:Pcr', 'pcr', 'WITH', 'dna.id = pcr.dnaFk')
      ->leftJoin('App:Chromatogram', 'ch', 'WITH', 'pcr.id = ch.pcrFk')
      ->leftJoin('App:InternalSequenceAssembly', 'at', 'WITH', 'at.chromatogramFk = ch.id')
      ->leftJoin('App:MotuDelimitation', 'ass', 'WITH', 'ass.internalSequenceFk = at.internalSequenceFk')
      ->leftJoin('App:InternalSequence', $seqAlias, 'WITH', "$seqAlias.id = at.internalSequenceFk")
      ->leftJoin('App:Voc', 'vocGene', 'WITH', 'vocGene.id = pcr.geneVocFk');
  }

  private function joinTaxonSite($query, $aliasEsp, $aliasSta) {
    return $query->leftJoin('App:InternalLot', 'lm', 'WITH', $aliasEsp . '.internalLotFk=lm.id')
      ->leftJoin('App:ExternalLot', 'lmext', 'WITH', $aliasEsp . '.externalLotFk=lmext.id')
      ->join('App:Sampling', 'c', 'WITH', 'c.id=lm.samplingFk OR c.id=lmext.samplingFk')
      ->join('App:Site', $aliasSta, 'WITH', $aliasSta . '.id=c.siteFk');
  }

  private function joinMotuCountMorpho($query, $alias = 'ass') {
    return $query->leftJoin('App:ExternalSequence', 'motu_sext', 'WITH', "motu_sext.id=$alias.externalSequenceFk")
      ->leftJoin('App:InternalSequenceAssembly', 'motu_at', 'WITH', "motu_at.internalSequenceFk = $alias.internalSequenceFk")
      ->leftJoin('App:Chromatogram', 'motu_chr', 'WITH', "motu_chr.id = motu_at.chromatogramFk")
      ->leftJoin('App:Pcr', 'motu_pcr', 'WITH', "motu_pcr.id = motu_chr.pcrFk")
      ->leftJoin('App:Dna', 'motu_dna', 'WITH', "motu_dna.id = motu_pcr.dnaFk")
      ->leftJoin('App:Specimen', 'motu_ind', 'WITH', "motu_ind.id = motu_dna.specimenFk")
      ->join('App:EspeceIdentifiée', 'motu_eid', 'WITH', "motu_eid.specimenFk = motu_ind.id OR motu_eid.externalSequenceFk=motu_sext.id")
      ->join('App:Voc', 'motu_voc', 'WITH', "motu_voc.id = $alias.methodVocFk")
      ->join('App:MotuDataset', 'motu_date', 'WITH', "motu_date.id = $alias.motuDatasetFk");
  }

  /*****************************************************************************
   * QUERIES
   *****************************************************************************/
  public function getMotuCountList($data) {

    $level = $data->get('level');
    $methods = $data->get('methods');
    $dataset = $data->get('dataset');
    $criteria = $data->get('criteria');

    $qb = $this->entityManager->createQueryBuilder();
    $query = $qb->select('rt.taxname as taxon, rt.id')
      ->addSelect('vocabulary.id as id_method, vocabulary.code as method')
      ->addSelect('motu_dataset.id as id_dataset, motu_dataset.date as dataset_date, motu_dataset.title as dataset')
      ->addSelect('COUNT(DISTINCT ass.motuNumber ) as count_motus')
      ->from('App:Taxon', 'rt')
      ->join('App:TaxonIdentification', 'e', 'WITH', 'rt.id = e.taxonFk');
    switch ($level) {
    case 1: #lot matériel
      $query = $query->join('App:InternalLot', 'lm', 'WITH', 'lm.id=e.internalLotFk')
        ->join('App:Specimen', 'i', 'WITH', 'i.internalLotFk = lm.id');
      $query = $this->joinIndivSeq($query, 'i', 'seq')->addSelect('COUNT(DISTINCT seq.id) as count_seq');
      break;

    case 2: #specimen
      $query = $query->join('App:Specimen', 'i', 'WITH', 'i.id = e.specimenFk');
      $query = $this->joinIndivSeq($query, 'i', 'seq')->addSelect('COUNT(DISTINCT seq.id) as count_seq');
      break;

    case 3: # sequence
      $query = $query->leftJoin('App:InternalSequence', 'seq', 'WITH', 'seq.id=e.internalSequenceFk')
        ->leftJoin('App:ExternalSequence', 'seqext', 'WITH', 'seqext.id=e.externalSequenceFk')
        ->join('App:MotuDelimitation', 'ass', 'WITH', 'ass.externalSequenceFk=seqext.id OR ass.internalSequenceFk=seq.id')
        ->addSelect('(COUNT(DISTINCT seq.id) + COUNT(DISTINCT seqext.id)) as count_seq');
      break;
    }

    $query = $query->join('App:MotuDataset', 'motu_dataset', 'WITH', 'ass.motuDatasetFk = motu_dataset.id')
      ->join('App:Voc', 'vocabulary', 'WITH', 'ass.methodVocFk = vocabulary.id');

    if ($data->get('species')) {
      $query = $query->andWhere('rt.species = :species')
        ->andWhere('rt.genus = :genus')
        ->setParameters([
          'genus' => $data->get('genus'),
          'species' => $data->get('species'),
        ]);
    }

    if ($criteria) {
      $query = $query->andWhere('e.identificationCriterionVocFk IN(:criteria)')
        ->setParameter('criteria', $criteria);
    }

    if ($methods) {
      $query = $query->andWhere('ass.methodVocFk IN(:methods)')
        ->setParameter('methods', $methods);
    }

    $query = $query->andWHere("vocabulary.code != 'HAPLO'")
      ->andWhere('motu_dataset.id = :id_dataset')
      ->setParameter('id_dataset', $dataset)
      ->groupBy('rt.id, rt.taxname, vocabulary.id, vocabulary.code, motu_dataset.id')
      ->orderBy('rt.id')
      ->getQuery();

    return $query->getArrayResult();
  }

  public function getMotuSeqList($data) {
    $id_taxon = $data->get('taxon');
    $id_method = $data->get('method');
    $dataset = $data->get('dataset');
    $level = $data->get('level');
    $criteria = $data->get('criteria');

    $qb = $this->entityManager->createQueryBuilder();
    $query = $qb->select('rt.id as idesp, rt.taxname')
      ->addSelect('vocabulary.code as method')
      ->addSelect('m.date as motu_date')
      ->addSelect('seq.id, seq.code as code, seq.accessionNumber as acc')
      ->addSelect('ass.motuNumber as motu_dataset')
      ->addSelect('v.code as criterion')
      ->addSelect('vocGene.code as gene')
      ->from('App:Taxon', 'rt')
      ->join('App:TaxonIdentification', 'e', 'WITH', 'rt.id = e.taxonFk')
      ->join('App:Voc', 'v', 'WITH', 'e.identificationCriterionVocFk=v.id');
    switch ($level) {
    case 1: # Bio material
      $query = $query->join('App:InternalLot', 'lm', 'WITH', 'lm.id=e.internalLotFk')
        ->join('App:Specimen', 'i', 'WITH', 'i.internalLotFk = lm.id');
      $query = $this->joinIndivSeq($query, 'i', 'seq');
      break;

    case 2: # Specimen
      $query = $query->join('App:Specimen', 'i', 'WITH', 'i.id = e.specimenFk');
      $query = $this->joinIndivSeq($query, 'i', 'seq');
      break;

    case 3: # Sequence
      $query = $query->leftJoin('App:InternalSequence', 'seq', 'WITH', 'seq.id=e.internalSequenceFk')
        ->leftJoin('App:ExternalSequence', 'seqext', 'WITH', 'seqext.id=e.externalSequenceFk')
        ->leftJoin('App:InternalSequenceAssembly', 'chrom_proc', 'WITH', 'chrom_proc.internalSequenceFk = seq.id')
        ->leftJoin('App:Chromatogram', 'chromatogram', 'WITH', 'chrom_proc.chromatogramFk = chromatogram.id')
        ->leftJoin('App:Pcr', 'pcr', 'WITH', 'chromatogram.pcrFk = pcr.id')
        ->join('App:MotuDelimitation', 'ass', 'WITH', 'ass.externalSequenceFk=seqext.id OR ass.internalSequenceFk=seq.id')
        ->join('App:Voc', 'vocGene', 'WITH', 'vocGene.id=seqext.geneVocFk OR vocGene.id=pcr.geneVocFk')
        ->addSelect('seqext.id as id_ext, seqext.code as codeExt, seqext.accessionNumber as acc_ext');
      break;
    }

    $query = $query->join('App:MotuDataset', 'm', 'WITH', 'ass.motuDatasetFk = m.id')
      ->join('App:Voc', 'vocabulary', 'WITH', 'ass.methodVocFk = vocabulary.id')
      ->andWhere('rt.id = :id_taxon')
      ->andWhere('vocabulary.id = :method')
      ->andWhere('m.id = :dataset')
      ->setParameters([
        'id_taxon' => $id_taxon,
        'method' => $id_method,
        'dataset' => $dataset,
      ]);

    if ($criteria) {
      $query = $query->andWhere('e.identificationCriterionVocFk IN (:criteria)')
        ->setParameter('criteria', $criteria);
    }

    $query = $query->distinct()->getQuery();

    $res = $query->getArrayResult();

    # fusion des résultats séquences internes/externes
    foreach ($res as $key => $row) {
      $res[$key]['type'] = ($row['id']) ? 0 : 1;
      $res[$key]['id'] = ($row['id']) ? $row['id'] : $row['id_ext'];
      $res[$key]['acc'] = ($row['acc']) ? $row['acc'] : $row['acc_ext'];
      $res[$key]['code'] = ($row['code']) ? $row['code'] : $row['codeExt'];
    }
    return $res;
  }

  public function getSpeciesAssignment($data) {
    $columnsMap = array(
      'biomaterial' => 'biomat',
      'specimen' => 'spec',
      'sequence' => 'seqrt',
    );
    $typeConstraints = array_fill_keys(
      ["A", "B", "C"],
      array()
    );
    foreach ($data as $key => $value) {
      if (in_array($value, array_keys($typeConstraints))) {
        $typeConstraints[$value][] = $columnsMap[$key];
      }
    }
    $undefinedSeq = ($data['sequence'] == '1');

    $qb = $this->entityManager->createQueryBuilder();
    $query =
    $qb->select('lm.id as id_lm, lm.code as code_lm')
      ->addSelect('biomat.id as idtax_lm, biomat.taxname as taxname_lm') // taxon lot matériel
      ->addSelect('lmvoc.code as criterion_code_biomat, lmvoc.libelle as criterion_title_biomat') // critere lot matériel
    // ->addSelect('indiv as ind') // specimen
      ->addSelect('indiv.id as id_indiv, indiv.molecularCode as code_biomol, indiv.morphologicalCode as code_tri_morpho') // specimen
      ->addSelect('spec.id as idtax_indiv, spec.taxname as taxname_indiv') // taxon specimen
      ->addSelect('ivoc.code as criterion_code_specimen, ivoc.libelle as criterion_title_specimen') // critere specimen
      ->addSelect('seq.id as id_seq, seq.code as code_seq') // séquence
      ->addSelect('seqrt.id as idtax_seq, seqrt.taxname as taxname_seq') // taxon séquence
      ->addSelect('seqvoc.code as criterion_code_seq, seqvoc.libelle as criterion_title_seq') // critere sequence
    // JOIN lot matériel
      ->from('App:InternalLot', 'lm')
      ->join('App:TaxonIdentification', 'eidlm', 'WITH', 'lm.id = eidlm.internalLotFk')
      ->join('App:Taxon', 'biomat', 'WITH', 'biomat.id = eidlm.taxonFk')
      ->join('App:Voc', 'lmvoc', 'WITH', 'eidlm.identificationCriterionVocFk=lmvoc.id')
    // JOIN specimen
      ->join('App:Specimen', 'indiv', 'WITH', 'indiv.internalLotFk = lm.id')
      ->join('App:TaxonIdentification', 'eidindiv', 'WITH', 'indiv.id = eidindiv.specimenFk')
      ->join('App:Taxon', 'spec', 'WITH', 'spec.id = eidindiv.taxonFk')
      ->join('App:Voc', 'ivoc', 'WITH', 'eidindiv.identificationCriterionVocFk=ivoc.id');
    // JOIN sequence
    $query = $this->leftJoinIndivSeq($query, 'indiv', 'seq')
      ->leftJoin('App:TaxonIdentification', 'eidseq', 'WITH', 'seq.id = eidseq.internalSequenceFk')
      ->leftJoin('App:Taxon', 'seqrt', 'WITH', 'seqrt.id = eidseq.taxonFk')
      ->leftJoin('App:Voc', 'seqvoc', 'WITH', 'eidseq.identificationCriterionVocFk=seqvoc.id');
    if ($undefinedSeq) {
      $query = $query->andWhere('seq.id IS NULL');
    }
    // FILTER based on user defined constraints
    $visited = [];
    foreach ($typeConstraints as $type => $identicals) {
      if ($identicals) {
        $current = [];
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
    $res = $query->getArrayResult();

    return array_map(function ($row) {
      return [
        "biomaterial" => [
          "id" => $row['id_lm'],
          "code" => $row['code_lm'],
          "taxname" => $row['taxname_lm'],
          "criterion" => [
            "code" => $row['criterion_code_biomat'],
            "title" => $row['criterion_title_biomat'],
          ],
        ],
        "specimen" => [
          "id" => $row["id_indiv"],
          "code" => [
            "biomol" => $row["code_biomol"],
            "morpho" => $row["code_tri_morpho"],
          ],
          "taxname" => $row["taxname_indiv"],
          "criterion" => [
            "code" => $row["criterion_code_specimen"],
            "title" => $row["criterion_title_specimen"],
          ],
        ],
        "sequence" => [
          "id" => $row["id_seq"],
          "code" => $row["code_seq"],
          "taxname" => $row["taxname_seq"],
          "criterion" => [
            "code" => $row["criterion_code_seq"],
            "title" => $row["criterion_title_seq"],
          ],
        ],
      ];
    }, $res);

  }

  public function getSpeciesSamplingDetails($id) {

    $biomat_ext = "SELECT DISTINCT eid.taxon_fk AS taxon_id,
      lmext.id as lm_id,
      site.id as site_id,
      'ext_biomat' as sample
    FROM identified_species eid
      JOIN external_biological_material lmext ON eid.external_biological_material_fk = lmext.id
      JOIN sampling co ON co.id = lmext.sampling_fk
      JOIN site ON co.site_fk = site.id";

    $biomat_int = "SELECT DISTINCT eid.taxon_fk AS taxon_id,
      lm.id as lm_id,
      site.id as site_id,
      'int_biomat' as sample
    FROM identified_species eid
      JOIN internal_biological_material lm ON eid.internal_biological_material_fk = lm.id
      JOIN sampling co ON co.id = lm.sampling_fk
      JOIN site ON co.site_fk = site.id";

    $co1_subquery = "SELECT DISTINCT eid.taxon_fk AS taxon_id,
      lm.id as lm_id,
      site.id as site_id,
      'CO1' as sample
    FROM identified_species eid
      LEFT JOIN external_sequence sext ON eid.external_sequence_fk = sext.id
      LEFT JOIN vocabulary v1 ON v1.id = sext.gene_voc_fk
      LEFT JOIN internal_sequence seq ON eid.internal_sequence_fk = seq.id
      LEFT JOIN chromatogram_is_processed_to eat ON eat.internal_sequence_fk = seq.id
      LEFT JOIN chromatogram chr ON chr.id = eat.chromatogram_fk
      LEFT JOIN pcr ON chr.pcr_fk = pcr.id
      LEFT JOIN vocabulary v2 ON pcr.gene_voc_fk = v2.id
      LEFT JOIN vocabulary seq_status ON seq_status.id = seq.internal_sequence_status_voc_fk
      LEFT JOIN dna ON pcr.dna_fk = dna.id
      LEFT JOIN specimen ind ON ind.id = dna.specimen_fk
      LEFT JOIN internal_biological_material lm ON ind.internal_biological_material_fk = lm.id
      JOIN sampling co ON co.id = sext.sampling_fk
      OR co.id = lm.sampling_fk
      JOIN site ON co.site_fk = site.id
    WHERE v1.code = 'COI'
      OR v2.code = 'COI'
      AND (
        seq_status.code = 'SHORT'
        OR seq_status.code LIKE 'VALID%'
      )";

    $rawSql = "WITH
    ext_lm as ($biomat_ext),
    int_lm as ($biomat_int),
    co1 as ($co1_subquery)";

    $rawSql .= "SELECT distinct
      s.id as site_id,
      s.site_code as site_code,
      s.latitude as latitude,
      s.longitude as longitude,
      s.elevation as altitude,
      m.municipality_name as municipality,
      c.country_name as country,
      count(distinct sample) as sampling_types_count,
      max(CASE WHEN sample = 'CO1' THEN 1 ELSE 0 END) as has_co1,
      max(CASE WHEN sample = 'int_biomat' THEN 1 ELSE 0 END) as int_biomat,
      max(CASE WHEN sample = 'ext_biomat' THEN 1 ELSE 0 END) as ext_biomat
    FROM (
        SELECT * FROM co1
        UNION
        SELECT * FROM int_lm
        UNION
        SELECT * FROM ext_lm
      ) subquery
      JOIN site s ON s.id = site_id
      JOIN taxon t ON t.id = taxon_id
      LEFT JOIN municipality m ON m.id = s.municipality_fk
      LEFT JOIN country c ON c.id = s.country_fk
    WHERE taxon_id = :taxon_id
    GROUP BY t.id,
      s.id, s.site_code,
      s.latitude, s.longitude, s.elevation,
      m.municipality_name, c.country_name";

    $stmt = $this->entityManager->getConnection()->prepare($rawSql);
    $stmt->execute(['taxon_id' => $id]);

    return $stmt->fetchAll();
  }

  public function getSpeciesGeoSummary($data, $co1 = false) {

    // Prepare subquery to list sites depending on CO1 sampling events requested (or not)
    $FIELD_SUFFIX = $co1 ? "_co1" : "";

    if ($co1) {
      $site_subquery = "SELECT DISTINCT
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
      $site_subquery = "SELECT DISTINCT
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

    $rawSql = "WITH esta AS ($site_subquery),
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
        'genus' => $data->get('genus'),
        'species' => $data->get('species'),
      ));
    } else {
      $stmt->execute();
    }
    return $stmt->fetchAll(\PDO::FETCH_UNIQUE | \PDO::FETCH_ASSOC);
  }

  public function getMotuGeoLocation($data) {

    $taxid = $data->get('taxname');
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
            site.id as site_id,
            site.elevation as altitude,
            site.latitude as latitude,
            site.longitude as longitude,
            site.site_code as site_code,
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
