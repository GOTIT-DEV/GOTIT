-- UPGRADE DATABASE TO v3.1 GOTIT version

-- ADD stock field (1/0) TO specimen table
ALTER TABLE IF EXISTS public.specimen
    ADD COLUMN IF NOT EXISTS stock smallint NOT NULL DEFAULT 1;
	
-- CREATE N-N TABLE ibm_sb	
CREATE TABLE public.ibm_sb
(    internal_biological_material_fk bigint NOT NULL,
     storage_box_fk bigint NOT NULL
);


-- ADD FK CONSTRAINT ON internal_biological_material_fk
ALTER TABLE public.ibm_sb 
  ADD CONSTRAINT fk_internal_biological_material_fk
    FOREIGN KEY (internal_biological_material_fk)
      REFERENCES internal_biological_material
        ON DELETE CASCADE
        ON UPDATE NO ACTION;

-- ADD FK CONSTRAINT ON storage_box_fk
ALTER TABLE public.ibm_sb 
  ADD CONSTRAINT fk_storage_box_fk
    FOREIGN KEY (storage_box_fk)
      REFERENCES storage_box
        ON DELETE CASCADE
        ON UPDATE NO ACTION;

-- INSERT EXISTING VALUES IN ibm_sb
INSERT INTO ibm_sb (internal_biological_material_fk, storage_box_fk)
select id, storage_box_fk from internal_biological_material where storage_box_fk IS NOT NULL;

-- DELETE field storage_box_fk from internal_biological_material table
UPDATE internal_biological_material SET storage_box_fk = NULL;
ALTER TABLE internal_biological_material  DROP COLUMN storage_box_fk;