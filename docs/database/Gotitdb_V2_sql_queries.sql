/**
 Malard et al. 2019. 
 GOTIT: A laboratory application software for optimizing multi-criteria species based research. 
 Submitted to Methods in Ecology and Evolution.
 
 Appendix S4. SQL queries
 
 GOTIT database help: Appendix S6 (S6_Gotitdb_sql_queries.txt). 
 A list of selected SQL queries to recover different subsets of data from GOTIT database.
 */
/*--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 Query 1: 
 This query returns multiple-criteria species data sets in which individual specimens 
 are attributed to multiple species hypotheses. 
 The data set can then be used to unveil geographic variation in species richness 
 among the different sets of species hypotheses.
 */
WITH liste_motus AS (
  SELECT code_sqc,
    internal_sequence_accession_number,
    vocabulary.code as methode,
    motu.motu_title,
    motu_number as motu
  FROM (
      SELECT internal_sequence.internal_sequence_code as code_sqc,
        internal_sequence_accession_number,
        motu_number.delimitation_method_voc_fk as methode_voc,
        motu_number.motu_number,
        motu_number.motu_fk
      FROM motu_number
        JOIN internal_sequence ON motu_number.internal_sequence_fk = internal_sequence.id
      UNION
      SELECT external_sequence.external_sequence_code as code_sqc,
        external_sequence_accession_number as internal_sequence_accession_number,
        motu_number.delimitation_method_voc_fk as methode_voc,
        motu_number.motu_number,
        motu_number.motu_fk
      FROM motu_number
        JOIN external_sequence ON motu_number.external_sequence_fk = external_sequence.id
    ) as seq
    JOIN vocabulary ON vocabulary.id = seq.methode_voc
    JOIN motu ON motu.id = seq.motu_fk
)
SELECT seq.code_sqc,
  seq.internal_sequence_accession_number,
  seq.delimitation,
  TH13.motu as TH_2013,
  GMYC.motu as GMYC_2013,
  TH17.motu as TH_2017,
  PTP.motu as PTP_2017,
  bPTP.motu as bPTP_2017,
  tax.taxon_name,
  site.latitude as latitude,
  site.longitude as longitude,
  sampling.sample_code,
  site.site_code,
  site.site_name,
  country.country_name
FROM (
    SELECT code_sqc,
      internal_sequence_accession_number,
      sampling_fk,
      rt,
      delimitation
    FROM (
        SELECT external_sequence_code as code_sqc,
          external_sequence_accession_number as internal_sequence_accession_number,
          sampling_fk,
          ei.taxon_fk as rt,
          critere.code as delimitation
        FROM external_sequence
          LEFT JOIN identified_species ei on ei.external_sequence_fk = external_sequence.id
          LEFT JOIN vocabulary critere ON ei.identification_criterion_voc_fk = critere.id
        UNION
        SELECT seqas.internal_sequence_code as code_sqc,
          seqas.internal_sequence_accession_number,
          lmat.sampling_fk,
          ei.taxon_fk as rt,
          critere.code as delimitation
        FROM internal_biological_material lmat
          JOIN specimen I ON I.internal_biological_material_fk = lmat.id
          JOIN dna ON dna.specimen_fk = I.id
          JOIN pcr ON pcr.dna_fk = dna.id
          JOIN chromatogram ON chromatogram.pcr_fk = pcr.id
          JOIN chromatogram_is_processed_to eaet ON chromatogram.id = eaet.chromatogram_fk
          JOIN internal_sequence seqas ON seqas.id = eaet.internal_sequence_fk
          LEFT JOIN identified_species ei on ei.internal_sequence_fk = seqas.id
          LEFT JOIN vocabulary critere ON ei.identification_criterion_voc_fk = critere.id
      ) AS union_seq
  ) AS seq
  LEFT JOIN taxon tax ON tax.id = seq.rt
  JOIN sampling ON seq.sampling_fk = sampling.id
  JOIN site ON sampling.site_fk = site.id
  JOIN municipality ON site.municipality_fk = municipality.id
  JOIN country ON site.country_fk = country.id
  LEFT JOIN (
    SELECT *
    FROM liste_motus
    WHERE motu_title = 'Morvan_et_al_2013_Syst_Biol'
      AND methode = 'GMYC'
  ) GMYC ON seq.code_sqc = GMYC.code_sqc
  LEFT JOIN (
    SELECT *
    FROM liste_motus
    WHERE motu_title = 'Morvan_et_al_2013_Syst_Biol'
      AND methode = 'TH'
  ) TH13 ON seq.code_sqc = TH13.code_sqc
  LEFT JOIN (
    SELECT *
    FROM liste_motus
    WHERE motu_title = 'Eme_et_al_2018_Ecography'
      AND methode = 'TH'
  ) TH17 ON seq.code_sqc = TH17.code_sqc
  LEFT JOIN (
    SELECT *
    FROM liste_motus
    WHERE motu_title = 'Eme_et_al_2018_Ecography'
      AND methode = 'PTP'
  ) PTP ON seq.code_sqc = PTP.code_sqc
  LEFT JOIN (
    SELECT *
    FROM liste_motus
    WHERE motu_title = 'Eme_et_al_2018_Ecography'
      AND methode = 'BPTP'
  ) BPTP ON seq.code_sqc = BPTP.code_sqc
WHERE bPTP.motu IS NOT NULL
  OR PTP.motu IS NOT NULL
  OR TH17.motu IS NOT NULL
ORDER BY internal_sequence_accession_number;
/*------------------------------------------------------------------------------
 Query 2: 
 This query returns the available vouchers, here the specimen lots containing 
 ovigerous females, as well as a number of data and metadata for exploring differences 
 in species reproductive traits (e.g. number and size of eggs) among habitats, 
 herein surface water and groundwater.
 */
-- (1) Create first the function compt_nb_total_specimens(id_internal_biological_material bigint) - PL/pgSQL language
CREATE FUNCTION compt_nb_total_specimens(id_internal_biological_material bigint) RETURNS integer LANGUAGE plpgsql AS $$
DECLARE nb_tot_specimens INT;
BEGIN
SELECT SUM(
    composition_of_internal_biological_material.number_of_specimens
  ) INTO nb_tot_specimens
FROM composition_of_internal_biological_material
  JOIN internal_biological_material lot ON lot.id = composition_of_internal_biological_material.internal_biological_material_fk
WHERE composition_of_internal_biological_material.internal_biological_material_fk = id_internal_biological_material
GROUP BY composition_of_internal_biological_material.internal_biological_material_fk;
RETURN nb_tot_specimens;
END;
$$;
-- (2) Execute the SQL Query  
SELECT internal_biological_material.internal_biological_material_code,
  voc_pigmentation.code as code_pigmentation,
  voc_yeux.code as code_eye,
  storage_box.box_code as code_box,
  rt.taxon_name,
  voc_critere_identification.code as code_identification,
  site.site_code,
  site.latitude,
  site.longitude,
  voc_habitat_type.code as code_habitat,
  voc_point_acces.code as code_point_acces,
  sampling.sampling_date,
  composition_of_internal_biological_material.internal_biological_material_composition_comments,
  compt_nb_total_specimens(
    composition_of_internal_biological_material.internal_biological_material_fk
  ) as nb_tot_specimens,
  composition_of_internal_biological_material.number_of_specimens as nb_BOV
FROM composition_of_internal_biological_material
  JOIN vocabulary voc_type_specimen ON voc_type_specimen.id = composition_of_internal_biological_material.specimen_type_voc_fk
  JOIN internal_biological_material ON internal_biological_material.id = composition_of_internal_biological_material.internal_biological_material_fk
  LEFT JOIN vocabulary voc_pigmentation ON voc_pigmentation.id = internal_biological_material.pigmentation_voc_fk
  LEFT JOIN vocabulary voc_yeux ON voc_yeux.id = internal_biological_material.eyes_voc_fk
  LEFT JOIN storage_box ON storage_box.id = internal_biological_material.storage_box_fk
  JOIN identified_species ei_lot ON ei_lot.internal_biological_material_fk = internal_biological_material.id
  INNER JOIN (
    SELECT MAX(ei_loti.id) AS maxei_loti
    FROM identified_species ei_loti
    GROUP BY ei_loti.internal_biological_material_fk
  ) ei_lot2 ON (ei_lot.id = ei_lot2.maxei_loti)
  JOIN vocabulary voc_critere_identification ON voc_critere_identification.id = ei_lot.identification_criterion_voc_fk
  JOIN taxon rt ON ei_lot.taxon_fk = rt.id
  JOIN sampling ON sampling.id = internal_biological_material.sampling_fk
  LEFT JOIN vocabulary voc_date_precision ON voc_date_precision.id = sampling.date_precision_voc_fk
  JOIN site ON site.id = sampling.site_fk
  LEFT JOIN vocabulary voc_habitat_type ON voc_habitat_type.id = site.habitat_type_voc_fk
  LEFT JOIN vocabulary voc_point_acces ON voc_point_acces.id = site.access_point_voc_fk
WHERE voc_type_specimen.code = 'BOV';
/*------------------------------------------------------------------------------
 Query 3: 
 This query returns the available sequence material for implementing a concatenated phylogenetic tree: 
 that is all sequence identifier codes, their geographic locations and attributed 
 morphospecies for specimens having 4 targeted genes successfully sequenced
 */
SELECT foo.liste_gene,
  foo.liste_chromato,
  foo.liste_sa,
  foo.liste_sa_code_sqc_ass,
  foo.site_code,
  foo.latitude,
  foo.longitude,
  foo.internal_biological_material_code,
  foo.taxname_lot,
  foo.specimen_morphological_code,
  foo.specimen_molecular_code,
  foo.taxname_ind,
  foo.taxname_sqc_ass
FROM (
    SELECT st.site_code,
      st.latitude,
      st.longitude,
      lot.internal_biological_material_code,
      rt_lot.taxon_name as taxname_lot,
      ei_lot.identification_date as date_identification_lot,
      ind.specimen_morphological_code,
      ind.specimen_molecular_code,
      rt_ind.taxon_name as taxname_ind,
      ei_ind.identification_date as identification_date_ind,
      rt_sqc_ass.taxon_name as taxname_sqc_ass,
      string_agg(voc_gene.code, ';') as liste_gene,
      string_agg(chromato.chromatogram_code, ' ;') as liste_chromato,
      string_agg(cast(sa.id as character varying), ' ;') as liste_sa,
      string_agg(sa.internal_sequence_code, ' ;') as liste_sa_code_sqc_ass
    FROM internal_biological_material lot
      JOIN sampling col ON col.id = lot.sampling_fk
      JOIN site st ON st.id = col.site_fk
      LEFT JOIN identified_species ei_lot ON ei_lot.internal_biological_material_fk = lot.id
      INNER JOIN (
        SELECT MAX(ei_loti.id) AS maxei_loti
        FROM identified_species ei_loti
        GROUP BY ei_loti.internal_biological_material_fk
      ) ei_lot2 ON (ei_lot.id = ei_lot2.maxei_loti)
      LEFT JOIN taxon rt_lot ON ei_lot.taxon_fk = rt_lot.id
      JOIN specimen ind ON ind.internal_biological_material_fk = lot.id
      LEFT JOIN identified_species ei_ind ON ei_ind.specimen_fk = ind.id
      INNER JOIN (
        SELECT MAX(ei_indi.id) AS maxei_indi
        FROM identified_species ei_indi
        GROUP BY ei_indi.specimen_fk
      ) ei_ind2 ON (ei_ind.id = ei_ind2.maxei_indi)
      LEFT JOIN taxon rt_ind ON ei_ind.taxon_fk = rt_ind.id
      JOIN dna ON dna.specimen_fk = ind.id
      JOIN pcr ON pcr.dna_fk = dna.id
      LEFT JOIN vocabulary voc_gene ON pcr.gene_voc_fk = voc_gene.id
      JOIN chromatogram chromato ON chromato.pcr_fk = pcr.id
      JOIN chromatogram_is_processed_to eaet ON eaet.chromatogram_fk = chromato.id
      JOIN internal_sequence sa ON eaet.internal_sequence_fk = sa.id
      LEFT JOIN vocabulary voc_statut_sqc_ass ON sa.internal_sequence_status_voc_fk = voc_statut_sqc_ass.id
      LEFT JOIN identified_species ei_sqc_ass ON ei_sqc_ass.internal_sequence_fk = sa.id
      INNER JOIN (
        SELECT MAX(ei_sqc_assi.id) AS maxei_sqc_assi
        FROM identified_species ei_sqc_assi
        GROUP BY ei_sqc_assi.internal_sequence_fk
      ) ei_sqc_ass2 ON (ei_sqc_ass.id = ei_sqc_ass2.maxei_sqc_assi)
      LEFT JOIN taxon rt_sqc_ass ON ei_sqc_ass.taxon_fk = rt_sqc_ass.id
    WHERE voc_statut_sqc_ass.code = 'VALID%'
    GROUP BY st.site_code,
      st.latitude,
      st.longitude,
      lot.internal_biological_material_code,
      rt_lot.taxon_name,
      ei_lot.identification_date,
      ind.specimen_morphological_code,
      ind.specimen_molecular_code,
      ind.specimen_molecular_code,
      rt_ind.taxon_name,
      ei_ind.identification_date,
      rt_sqc_ass.taxon_name
  ) as foo
GROUP BY foo.liste_gene,
  foo.liste_chromato,
  foo.liste_sa,
  foo.liste_sa_code_sqc_ass,
  foo.site_code,
  foo.latitude,
  foo.longitude,
  foo.internal_biological_material_code,
  foo.taxname_lot,
  foo.taxname_ind,
  foo.specimen_morphological_code,
  foo.specimen_molecular_code,
  foo.taxname_sqc_ass
HAVING foo.liste_gene LIKE '%16S%'
  AND foo.liste_gene LIKE '%COI%'
  AND foo.liste_gene LIKE '%AM4%'
  AND foo.liste_gene LIKE '%28S%'
ORDER BY foo.site_code ASC;
/*------------------------------------------------------------------------------
 Query 4: 
 This query provides a laboratory worker with all the necessary data to build 
 key indicators for selecting the most suitable PCR primers to obtain a DNA sequence 
 among all primers used for a given species and gene. The query can be refined 
 to select data for a single targeted gene and unique species.
 */
SELECT st.site_code,
  col.sample_code,
  lot.internal_biological_material_code,
  rt_lot.taxon_name as taxname_lot,
  ei_lot.identification_date as date_ei_lot,
  specimen.specimen_molecular_code,
  specimen.specimen_morphological_code,
  rt.taxon_name as taxname_ind,
  voc_critere_identification.code as critere_ei_ind,
  ei_ind.identification_date as date_ei_ind,
  dna.dna_code,
  voc_gene.code as gene,
  pcr.pcr_code,
  pcr.pcr_date,
  voc_date_precision_pcr.code as date_precision_pcr,
  voc_primer_pcr_start.code as primer_pcr_start_pcr,
  voc_primer_pcr_end.code as primer_pcr_end_pcr,
  voc_qualite_pcr.code as qualite_pcr,
  voc_specificite.code as specificite_pcr,
  pcr.pcr_details,
  pcr.pcr_comments,
  chromatogram.chromatogram_code,
  chromatogram.chromatogram_number,
  primer_chromato.code as primer_chromato_chromato,
  qualite_chromato.code as qualite_chromato,
  chromatogram.chromatogram_comments,
  sa.internal_sequence_code,
  sa.internal_sequence_alignment_code,
  voc_statut_sqc_ass.code as statut_sqc_ass,
  sa.internal_sequence_comments,
  rt_sqc_ass.taxon_name as taxname_sqc,
  voc_critere_identification_sqc_ass.code as critere_ei_sqc
FROM internal_biological_material lot
  LEFT JOIN identified_species ei_lot ON ei_lot.internal_biological_material_fk = lot.id
  INNER JOIN (
    SELECT MAX(ei_loti.id) AS maxei_loti
    FROM identified_species ei_loti
    GROUP BY ei_loti.internal_biological_material_fk
  ) ei_lot2 ON (ei_lot.id = ei_lot2.maxei_loti)
  LEFT JOIN vocabulary voc_date_precision_lot ON ei_lot.date_precision_voc_fk = voc_date_precision_lot.id
  LEFT JOIN vocabulary voc_critere_identification_lot ON ei_lot.identification_criterion_voc_fk = voc_critere_identification_lot.id
  LEFT JOIN taxon rt_lot ON ei_lot.taxon_fk = rt_lot.id
  JOIN sampling col ON col.id = lot.sampling_fk
  JOIN site st ON st.id = col.site_fk
  LEFT JOIN specimen ON specimen.internal_biological_material_fk = lot.id
  LEFT JOIN vocabulary voc1 ON specimen.specimen_type_voc_fk = voc1.id
  LEFT JOIN identified_species ei_ind ON ei_ind.specimen_fk = specimen.id
  INNER JOIN (
    SELECT MAX(ei_indi.id) AS maxei_indi
    FROM identified_species ei_indi
    GROUP BY ei_indi.specimen_fk
  ) ei_ind2 ON (ei_ind.id = ei_ind2.maxei_indi)
  LEFT JOIN vocabulary voc_date_precision ON ei_ind.date_precision_voc_fk = voc_date_precision.id
  LEFT JOIN vocabulary voc_critere_identification ON ei_ind.identification_criterion_voc_fk = voc_critere_identification.id
  LEFT JOIN taxon rt ON ei_ind.taxon_fk = rt.id
  LEFT JOIN dna ON dna.specimen_fk = specimen.id
  LEFT JOIN vocabulary voc_statut ON dna.dna_extraction_method_voc_fk = voc_statut.id
  LEFT JOIN pcr ON pcr.dna_fk = dna.id
  LEFT JOIN vocabulary voc_primer_pcr_start ON pcr.forward_primer_voc_fk = voc_primer_pcr_start.id
  LEFT JOIN vocabulary voc_primer_pcr_end ON pcr.reverse_primer_voc_fk = voc_primer_pcr_end.id
  LEFT JOIN vocabulary voc_gene ON pcr.gene_voc_fk = voc_gene.id
  LEFT JOIN vocabulary voc_qualite_pcr ON pcr.pcr_quality_voc_fk = voc_qualite_pcr.id
  LEFT JOIN vocabulary voc_date_precision_pcr ON pcr.date_precision_voc_fk = voc_date_precision_pcr.id
  LEFT JOIN vocabulary voc_specificite ON pcr.pcr_specificity_voc_fk = voc_specificite.id
  LEFT JOIN chromatogram ON chromatogram.pcr_fk = pcr.id
  LEFT JOIN vocabulary qualite_chromato ON chromatogram.chromato_quality_voc_fk = qualite_chromato.id
  LEFT JOIN vocabulary primer_chromato ON chromatogram.chromato_primer_voc_fk = primer_chromato.id
  LEFT JOIN chromatogram_is_processed_to eaet ON eaet.chromatogram_fk = chromatogram.id
  LEFT JOIN internal_sequence sa ON eaet.internal_sequence_fk = sa.id
  LEFT JOIN vocabulary voc_statut_sqc_ass ON sa.internal_sequence_status_voc_fk = voc_statut_sqc_ass.id
  LEFT JOIN identified_species ei_sqc_ass ON ei_sqc_ass.internal_sequence_fk = sa.id
  INNER JOIN (
    SELECT MAX(ei_sqc_assi.id) AS maxei_sqc_assi
    FROM identified_species ei_sqc_assi
    GROUP BY ei_sqc_assi.internal_sequence_fk
  ) ei_sqc_ass2 ON (ei_sqc_ass.id = ei_sqc_ass2.maxei_sqc_assi)
  LEFT JOIN vocabulary voc_date_precision_sqc_ass ON ei_sqc_ass.date_precision_voc_fk = voc_date_precision_sqc_ass.id
  LEFT JOIN vocabulary voc_critere_identification_sqc_ass ON ei_sqc_ass.identification_criterion_voc_fk = voc_critere_identification_sqc_ass.id
  LEFT JOIN taxon rt_sqc_ass ON ei_sqc_ass.taxon_fk = rt_sqc_ass.id
WHERE rt_sqc_ass.taxon_name LIKE '%'
  AND voc_gene.code LIKE '%'
ORDER BY lot.id,
  specimen.id,
  dna.id,
  pcr.id,
  chromatogram.id,
  sa.id ASC;