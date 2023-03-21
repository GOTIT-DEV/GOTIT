-- ADD a foreign key pcr_fk  in identified_species table
ALTER TABLE identified_species ADD COLUMN pcr_fk BIGINT; 
CREATE INDEX IDX_pcr_fk ON identified_species
(
  pcr_fk
);
ALTER TABLE identified_species 
  ADD CONSTRAINT fk_pcr_fk
    FOREIGN KEY (pcr_fk)
      REFERENCES pcr
        ON DELETE CASCADE
        ON UPDATE NO ACTION;
-- DROP NOT NULL CONSTRAINT ON Eyes AND Pigmentation
ALTER TABLE internal_biological_material ALTER pigmentation_voc_fk DROP NOT NULL;
ALTER TABLE internal_biological_material ALTER eyes_voc_fk DROP NOT NULL;