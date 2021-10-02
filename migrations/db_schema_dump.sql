--
-- PostgreSQL database dump
--

-- Dumped from database version 12.7 (Debian 12.7-1.pgdg100+1)
-- Dumped by pg_dump version 12.8 (Ubuntu 12.8-1.pgdg20.04+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: cube; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS cube WITH SCHEMA public;


--
-- Name: EXTENSION cube; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION cube IS 'data type for multidimensional cubes';


--
-- Name: earthdistance; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS earthdistance WITH SCHEMA public;


--
-- Name: EXTENSION earthdistance; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION earthdistance IS 'calculate great-circle distances on the surface of the Earth';


--
-- Name: compt_nb_total_specimens(bigint); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.compt_nb_total_specimens(id_internal_biological_material bigint) RETURNS integer
    LANGUAGE plpgsql
    AS $$
	DECLARE nb_tot_specimens INT;
    BEGIN	
	SELECT SUM(composition_of_internal_biological_material.number_of_specimens) INTO nb_tot_specimens
		FROM composition_of_internal_biological_material  
		JOIN internal_biological_material lot ON lot.id = composition_of_internal_biological_material.internal_biological_material_fk
        WHERE composition_of_internal_biological_material.internal_biological_material_fk = id_internal_biological_material
		GROUP BY  composition_of_internal_biological_material.internal_biological_material_fk 
	;
	RETURN nb_tot_specimens;	
	END;
$$;


--
-- Name: maj_datecre_datemaj_commune(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.maj_datecre_datemaj_commune() RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
  n INTEGER := 0 ;
  i INTEGER := 0 ;
BEGIN
	SELECT COUNT(*) FROM commune INTO n ; 
    WHILE  i<n LOOP 
           i := i + 1 ;
          UPDATE commune
            SET date_cre = '2018-07-23 15:33:17'
            WHERE id = i;
          UPDATE commune
            SET date_maj = '2018-07-23 15:33:17'
            WHERE id = i;
    END LOOP ;
    RETURN n ;
END ;
$$;


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: chromatogram; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.chromatogram (
    id bigint NOT NULL,
    chromatogram_code character varying(255) NOT NULL,
    chromatogram_number character varying(255) NOT NULL,
    chromatogram_comments text,
    chromato_primer_voc_fk bigint NOT NULL,
    chromato_quality_voc_fk bigint NOT NULL,
    institution_fk bigint NOT NULL,
    pcr_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: chromatogram_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.chromatogram_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: chromatogram_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.chromatogram_id_seq OWNED BY public.chromatogram.id;


--
-- Name: chromatogram_is_processed_to; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.chromatogram_is_processed_to (
    id bigint NOT NULL,
    chromatogram_fk bigint NOT NULL,
    internal_sequence_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: chromatogram_is_processed_to_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.chromatogram_is_processed_to_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: chromatogram_is_processed_to_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.chromatogram_is_processed_to_id_seq OWNED BY public.chromatogram_is_processed_to.id;


--
-- Name: composition_of_internal_biological_material; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.composition_of_internal_biological_material (
    id bigint NOT NULL,
    number_of_specimens bigint,
    internal_biological_material_composition_comments text,
    specimen_type_voc_fk bigint NOT NULL,
    internal_biological_material_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: composition_of_internal_biological_material_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.composition_of_internal_biological_material_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: composition_of_internal_biological_material_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.composition_of_internal_biological_material_id_seq OWNED BY public.composition_of_internal_biological_material.id;


--
-- Name: country; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.country (
    id bigint NOT NULL,
    country_code character varying(255) NOT NULL,
    country_name character varying(1024) NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: country_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.country_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: country_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.country_id_seq OWNED BY public.country.id;


--
-- Name: dna; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.dna (
    id bigint NOT NULL,
    dna_code character varying(255) NOT NULL,
    dna_extraction_date date,
    dna_concentration double precision,
    dna_comments text,
    date_precision_voc_fk bigint NOT NULL,
    dna_extraction_method_voc_fk bigint NOT NULL,
    specimen_fk bigint NOT NULL,
    dna_quality_voc_fk bigint NOT NULL,
    storage_box_fk bigint,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: dna_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.dna_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: dna_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.dna_id_seq OWNED BY public.dna.id;


--
-- Name: dna_is_extracted_by; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.dna_is_extracted_by (
    id bigint NOT NULL,
    dna_fk bigint NOT NULL,
    person_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: dna_is_extracted_by_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.dna_is_extracted_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: dna_is_extracted_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.dna_is_extracted_by_id_seq OWNED BY public.dna_is_extracted_by.id;


--
-- Name: external_biological_material; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.external_biological_material (
    id bigint NOT NULL,
    external_biological_material_code character varying(255) NOT NULL,
    external_biological_material_creation_date date,
    external_biological_material_comments text,
    number_of_specimens_comments text,
    sampling_fk bigint NOT NULL,
    date_precision_voc_fk bigint NOT NULL,
    number_of_specimens_voc_fk bigint NOT NULL,
    pigmentation_voc_fk bigint NOT NULL,
    eyes_voc_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: external_biological_material_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.external_biological_material_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: external_biological_material_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.external_biological_material_id_seq OWNED BY public.external_biological_material.id;


--
-- Name: external_biological_material_is_processed_by; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.external_biological_material_is_processed_by (
    id bigint NOT NULL,
    person_fk bigint NOT NULL,
    external_biological_material_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: external_biological_material_is_processed_by_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.external_biological_material_is_processed_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: external_biological_material_is_processed_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.external_biological_material_is_processed_by_id_seq OWNED BY public.external_biological_material_is_processed_by.id;


--
-- Name: external_biological_material_is_published_in; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.external_biological_material_is_published_in (
    id bigint NOT NULL,
    external_biological_material_fk bigint NOT NULL,
    source_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: external_biological_material_is_published_in_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.external_biological_material_is_published_in_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: external_biological_material_is_published_in_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.external_biological_material_is_published_in_id_seq OWNED BY public.external_biological_material_is_published_in.id;


--
-- Name: external_sequence; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.external_sequence (
    id bigint NOT NULL,
    external_sequence_code character varying(1024) NOT NULL,
    external_sequence_creation_date date,
    external_sequence_accession_number character varying(255) NOT NULL,
    external_sequence_alignment_code character varying(1024),
    external_sequence_specimen_number character varying(255) NOT NULL,
    external_sequence_primary_taxon character varying(255),
    external_sequence_comments text,
    gene_voc_fk bigint NOT NULL,
    date_precision_voc_fk bigint NOT NULL,
    external_sequence_origin_voc_fk bigint NOT NULL,
    sampling_fk bigint NOT NULL,
    external_sequence_status_voc_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: external_sequence_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.external_sequence_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: external_sequence_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.external_sequence_id_seq OWNED BY public.external_sequence.id;


--
-- Name: external_sequence_is_entered_by; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.external_sequence_is_entered_by (
    id bigint NOT NULL,
    external_sequence_fk bigint NOT NULL,
    person_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: external_sequence_is_entered_by_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.external_sequence_is_entered_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: external_sequence_is_entered_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.external_sequence_is_entered_by_id_seq OWNED BY public.external_sequence_is_entered_by.id;


--
-- Name: external_sequence_is_published_in; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.external_sequence_is_published_in (
    id bigint NOT NULL,
    source_fk bigint NOT NULL,
    external_sequence_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: external_sequence_is_published_in_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.external_sequence_is_published_in_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: external_sequence_is_published_in_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.external_sequence_is_published_in_id_seq OWNED BY public.external_sequence_is_published_in.id;


--
-- Name: has_targeted_taxa; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.has_targeted_taxa (
    id bigint NOT NULL,
    sampling_fk bigint NOT NULL,
    taxon_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: has_targeted_taxa_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.has_targeted_taxa_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: has_targeted_taxa_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.has_targeted_taxa_id_seq OWNED BY public.has_targeted_taxa.id;


--
-- Name: identified_species; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.identified_species (
    id bigint NOT NULL,
    identification_date date,
    identified_species_comments text,
    identification_criterion_voc_fk bigint NOT NULL,
    date_precision_voc_fk bigint NOT NULL,
    external_sequence_fk bigint,
    external_biological_material_fk bigint,
    internal_biological_material_fk bigint,
    taxon_fk bigint NOT NULL,
    specimen_fk bigint,
    internal_sequence_fk bigint,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint,
    type_material_voc_fk bigint
);


--
-- Name: identified_species_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.identified_species_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: identified_species_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.identified_species_id_seq OWNED BY public.identified_species.id;


--
-- Name: institution; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.institution (
    id bigint NOT NULL,
    institution_name character varying(1024) NOT NULL,
    institution_comments text,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: institution_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.institution_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: institution_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.institution_id_seq OWNED BY public.institution.id;


--
-- Name: internal_biological_material; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.internal_biological_material (
    id bigint NOT NULL,
    internal_biological_material_code character varying(255) NOT NULL,
    internal_biological_material_date date,
    sequencing_advice text,
    internal_biological_material_comments text,
    internal_biological_material_status smallint NOT NULL,
    date_precision_voc_fk bigint NOT NULL,
    pigmentation_voc_fk bigint NOT NULL,
    eyes_voc_fk bigint NOT NULL,
    sampling_fk bigint NOT NULL,
    storage_box_fk bigint,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: internal_biological_material_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.internal_biological_material_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: internal_biological_material_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.internal_biological_material_id_seq OWNED BY public.internal_biological_material.id;


--
-- Name: internal_biological_material_is_published_in; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.internal_biological_material_is_published_in (
    id bigint NOT NULL,
    internal_biological_material_fk bigint NOT NULL,
    source_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: internal_biological_material_is_published_in_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.internal_biological_material_is_published_in_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: internal_biological_material_is_published_in_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.internal_biological_material_is_published_in_id_seq OWNED BY public.internal_biological_material_is_published_in.id;


--
-- Name: internal_biological_material_is_treated_by; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.internal_biological_material_is_treated_by (
    id bigint NOT NULL,
    internal_biological_material_fk bigint NOT NULL,
    person_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: internal_biological_material_is_treated_by_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.internal_biological_material_is_treated_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: internal_biological_material_is_treated_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.internal_biological_material_is_treated_by_id_seq OWNED BY public.internal_biological_material_is_treated_by.id;


--
-- Name: internal_sequence; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.internal_sequence (
    id bigint NOT NULL,
    internal_sequence_code character varying(1024) NOT NULL,
    internal_sequence_creation_date date,
    internal_sequence_accession_number character varying(255),
    internal_sequence_alignment_code character varying(1024),
    internal_sequence_comments text,
    date_precision_voc_fk bigint NOT NULL,
    internal_sequence_status_voc_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: internal_sequence_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.internal_sequence_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: internal_sequence_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.internal_sequence_id_seq OWNED BY public.internal_sequence.id;


--
-- Name: internal_sequence_is_assembled_by; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.internal_sequence_is_assembled_by (
    id bigint NOT NULL,
    internal_sequence_fk bigint NOT NULL,
    person_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: internal_sequence_is_assembled_by_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.internal_sequence_is_assembled_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: internal_sequence_is_assembled_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.internal_sequence_is_assembled_by_id_seq OWNED BY public.internal_sequence_is_assembled_by.id;


--
-- Name: internal_sequence_is_published_in; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.internal_sequence_is_published_in (
    id bigint NOT NULL,
    source_fk bigint NOT NULL,
    internal_sequence_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: internal_sequence_is_published_in_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.internal_sequence_is_published_in_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: internal_sequence_is_published_in_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.internal_sequence_is_published_in_id_seq OWNED BY public.internal_sequence_is_published_in.id;


--
-- Name: motu; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.motu (
    id bigint NOT NULL,
    csv_file_name character varying(1024) NOT NULL,
    motu_date date NOT NULL,
    motu_comments text,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint,
    motu_title character varying(255) NOT NULL
);


--
-- Name: motu_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.motu_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: motu_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.motu_id_seq OWNED BY public.motu.id;


--
-- Name: motu_is_generated_by; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.motu_is_generated_by (
    id bigint NOT NULL,
    motu_fk bigint NOT NULL,
    person_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: motu_is_generated_by_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.motu_is_generated_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: motu_is_generated_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.motu_is_generated_by_id_seq OWNED BY public.motu_is_generated_by.id;


--
-- Name: motu_number; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.motu_number (
    id bigint NOT NULL,
    motu_number bigint NOT NULL,
    external_sequence_fk bigint,
    delimitation_method_voc_fk bigint NOT NULL,
    internal_sequence_fk bigint,
    motu_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: motu_number_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.motu_number_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: motu_number_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.motu_number_id_seq OWNED BY public.motu_number.id;


--
-- Name: municipality; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.municipality (
    id bigint NOT NULL,
    municipality_code character varying(255) NOT NULL,
    municipality_name character varying(1024) NOT NULL,
    region_name character varying(1024) NOT NULL,
    country_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: municipality_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.municipality_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: municipality_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.municipality_id_seq OWNED BY public.municipality.id;


--
-- Name: pcr; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.pcr (
    id bigint NOT NULL,
    pcr_code character varying(255) NOT NULL,
    pcr_number character varying(255) NOT NULL,
    pcr_date date,
    pcr_details text,
    pcr_comments text,
    gene_voc_fk bigint NOT NULL,
    pcr_quality_voc_fk bigint NOT NULL,
    pcr_specificity_voc_fk bigint NOT NULL,
    forward_primer_voc_fk bigint NOT NULL,
    reverse_primer_voc_fk bigint NOT NULL,
    date_precision_voc_fk bigint NOT NULL,
    dna_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: pcr_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.pcr_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: pcr_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.pcr_id_seq OWNED BY public.pcr.id;


--
-- Name: pcr_is_done_by; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.pcr_is_done_by (
    id bigint NOT NULL,
    pcr_fk bigint NOT NULL,
    person_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: pcr_is_done_by_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.pcr_is_done_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: pcr_is_done_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.pcr_is_done_by_id_seq OWNED BY public.pcr_is_done_by.id;


--
-- Name: person; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.person (
    id bigint NOT NULL,
    person_name character varying(255) NOT NULL,
    person_full_name character varying(1024),
    person_name_bis character varying(255),
    person_comments text,
    institution_fk bigint,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: person_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.person_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: person_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.person_id_seq OWNED BY public.person.id;


--
-- Name: program; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.program (
    id bigint NOT NULL,
    program_code character varying(255) NOT NULL,
    program_name character varying(1024) NOT NULL,
    coordinator_names text NOT NULL,
    funding_agency character varying(1024),
    starting_year bigint,
    ending_year bigint,
    program_comments text,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: program_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.program_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: program_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.program_id_seq OWNED BY public.program.id;


--
-- Name: sample_is_fixed_with; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sample_is_fixed_with (
    id bigint NOT NULL,
    fixative_voc_fk bigint NOT NULL,
    sampling_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: sample_is_fixed_with_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.sample_is_fixed_with_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: sample_is_fixed_with_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.sample_is_fixed_with_id_seq OWNED BY public.sample_is_fixed_with.id;


--
-- Name: sampling; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sampling (
    id bigint NOT NULL,
    sample_code character varying(255) NOT NULL,
    sampling_date date,
    sampling_duration bigint,
    temperature double precision,
    specific_conductance double precision,
    sample_status smallint NOT NULL,
    sampling_comments text,
    date_precision_voc_fk bigint NOT NULL,
    donation_voc_fk bigint NOT NULL,
    site_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: sampling_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.sampling_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: sampling_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.sampling_id_seq OWNED BY public.sampling.id;


--
-- Name: sampling_is_done_with_method; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sampling_is_done_with_method (
    id bigint NOT NULL,
    sampling_method_voc_fk bigint NOT NULL,
    sampling_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: sampling_is_done_with_method_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.sampling_is_done_with_method_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: sampling_is_done_with_method_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.sampling_is_done_with_method_id_seq OWNED BY public.sampling_is_done_with_method.id;


--
-- Name: sampling_is_funded_by; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sampling_is_funded_by (
    id bigint NOT NULL,
    program_fk bigint NOT NULL,
    sampling_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: sampling_is_funded_by_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.sampling_is_funded_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: sampling_is_funded_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.sampling_is_funded_by_id_seq OWNED BY public.sampling_is_funded_by.id;


--
-- Name: sampling_is_performed_by; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sampling_is_performed_by (
    id bigint NOT NULL,
    person_fk bigint NOT NULL,
    sampling_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: sampling_is_performed_by_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.sampling_is_performed_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: sampling_is_performed_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.sampling_is_performed_by_id_seq OWNED BY public.sampling_is_performed_by.id;


--
-- Name: site; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.site (
    id bigint NOT NULL,
    site_code character varying(255) NOT NULL,
    site_name character varying(1024) NOT NULL,
    latitude double precision NOT NULL,
    longitude double precision NOT NULL,
    elevation bigint,
    location_info text,
    site_description text,
    site_comments text,
    municipality_fk bigint NOT NULL,
    country_fk bigint NOT NULL,
    access_point_voc_fk bigint NOT NULL,
    habitat_type_voc_fk bigint NOT NULL,
    coordinate_precision_voc_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: site_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.site_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: site_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.site_id_seq OWNED BY public.site.id;


--
-- Name: slide_is_mounted_by; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.slide_is_mounted_by (
    id bigint NOT NULL,
    specimen_slide_fk bigint NOT NULL,
    person_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: slide_is_mounted_by_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.slide_is_mounted_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: slide_is_mounted_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.slide_is_mounted_by_id_seq OWNED BY public.slide_is_mounted_by.id;


--
-- Name: source; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.source (
    id bigint NOT NULL,
    source_code character varying(255) NOT NULL,
    source_year bigint,
    source_title character varying(2048) NOT NULL,
    source_comments text,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: source_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.source_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: source_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.source_id_seq OWNED BY public.source.id;


--
-- Name: source_is_entered_by; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.source_is_entered_by (
    id bigint NOT NULL,
    source_fk bigint NOT NULL,
    person_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: source_is_entered_by_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.source_is_entered_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: source_is_entered_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.source_is_entered_by_id_seq OWNED BY public.source_is_entered_by.id;


--
-- Name: species_is_identified_by; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.species_is_identified_by (
    id bigint NOT NULL,
    identified_species_fk bigint NOT NULL,
    person_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: species_is_identified_by_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.species_is_identified_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: species_is_identified_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.species_is_identified_by_id_seq OWNED BY public.species_is_identified_by.id;


--
-- Name: specimen; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.specimen (
    id bigint NOT NULL,
    specimen_molecular_code character varying(255),
    specimen_morphological_code character varying(255) NOT NULL,
    tube_code character varying(255) NOT NULL,
    specimen_molecular_number character varying(255),
    specimen_comments text,
    specimen_type_voc_fk bigint NOT NULL,
    internal_biological_material_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: specimen_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.specimen_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: specimen_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.specimen_id_seq OWNED BY public.specimen.id;


--
-- Name: specimen_slide; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.specimen_slide (
    id bigint NOT NULL,
    collection_slide_code character varying(255) NOT NULL,
    slide_title character varying(1024) NOT NULL,
    slide_date date,
    photo_folder_name character varying(1024),
    slide_comments text,
    date_precision_voc_fk bigint NOT NULL,
    storage_box_fk bigint,
    specimen_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: specimen_slide_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.specimen_slide_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: specimen_slide_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.specimen_slide_id_seq OWNED BY public.specimen_slide.id;


--
-- Name: storage_box; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.storage_box (
    id bigint NOT NULL,
    box_code character varying(255) NOT NULL,
    box_title character varying(1024) NOT NULL,
    box_comments text,
    collection_type_voc_fk bigint NOT NULL,
    collection_code_voc_fk bigint NOT NULL,
    box_type_voc_fk bigint NOT NULL,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: storage_box_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.storage_box_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: storage_box_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.storage_box_id_seq OWNED BY public.storage_box.id;


--
-- Name: taxon; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.taxon (
    id bigint NOT NULL,
    taxon_name character varying(255) NOT NULL,
    taxon_rank character varying(255) NOT NULL,
    subclass character varying(255),
    taxon_order character varying(255),
    family character varying(255),
    genus character varying(255),
    species character varying(255),
    subspecies character varying(255),
    taxon_validity smallint NOT NULL,
    taxon_code character varying(255) NOT NULL,
    taxon_comments text,
    clade character varying(255),
    taxon_synonym character varying(255),
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint,
    taxon_full_name character varying(255)
);


--
-- Name: taxon_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.taxon_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: taxon_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.taxon_id_seq OWNED BY public.taxon.id;


--
-- Name: user_db; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_db (
    id bigint NOT NULL,
    user_name character varying(255) NOT NULL,
    user_password character varying(255) NOT NULL,
    user_email character varying(255) DEFAULT NULL::character varying,
    user_role character varying(255) NOT NULL,
    salt character varying(255) DEFAULT NULL::character varying,
    user_full_name character varying(255) NOT NULL,
    user_institution character varying(255) DEFAULT NULL::character varying,
    date_of_creation timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    date_of_update timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint,
    user_is_active smallint NOT NULL,
    user_comments text
);


--
-- Name: user_db_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_db_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_db_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_db_id_seq OWNED BY public.user_db.id;


--
-- Name: vocabulary; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.vocabulary (
    id bigint NOT NULL,
    code character varying(255) NOT NULL,
    vocabulary_title character varying(1024) NOT NULL,
    parent character varying(255) NOT NULL,
    voc_comments text,
    date_of_creation timestamp without time zone,
    date_of_update timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint
);


--
-- Name: vocabulary_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.vocabulary_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: vocabulary_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.vocabulary_id_seq OWNED BY public.vocabulary.id;


--
-- Name: chromatogram id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.chromatogram ALTER COLUMN id SET DEFAULT nextval('public.chromatogram_id_seq'::regclass);


--
-- Name: chromatogram_is_processed_to id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.chromatogram_is_processed_to ALTER COLUMN id SET DEFAULT nextval('public.chromatogram_is_processed_to_id_seq'::regclass);


--
-- Name: composition_of_internal_biological_material id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.composition_of_internal_biological_material ALTER COLUMN id SET DEFAULT nextval('public.composition_of_internal_biological_material_id_seq'::regclass);


--
-- Name: country id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.country ALTER COLUMN id SET DEFAULT nextval('public.country_id_seq'::regclass);


--
-- Name: dna id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dna ALTER COLUMN id SET DEFAULT nextval('public.dna_id_seq'::regclass);


--
-- Name: dna_is_extracted_by id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dna_is_extracted_by ALTER COLUMN id SET DEFAULT nextval('public.dna_is_extracted_by_id_seq'::regclass);


--
-- Name: external_biological_material id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_biological_material ALTER COLUMN id SET DEFAULT nextval('public.external_biological_material_id_seq'::regclass);


--
-- Name: external_biological_material_is_processed_by id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_biological_material_is_processed_by ALTER COLUMN id SET DEFAULT nextval('public.external_biological_material_is_processed_by_id_seq'::regclass);


--
-- Name: external_biological_material_is_published_in id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_biological_material_is_published_in ALTER COLUMN id SET DEFAULT nextval('public.external_biological_material_is_published_in_id_seq'::regclass);


--
-- Name: external_sequence id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_sequence ALTER COLUMN id SET DEFAULT nextval('public.external_sequence_id_seq'::regclass);


--
-- Name: external_sequence_is_entered_by id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_sequence_is_entered_by ALTER COLUMN id SET DEFAULT nextval('public.external_sequence_is_entered_by_id_seq'::regclass);


--
-- Name: external_sequence_is_published_in id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_sequence_is_published_in ALTER COLUMN id SET DEFAULT nextval('public.external_sequence_is_published_in_id_seq'::regclass);


--
-- Name: has_targeted_taxa id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.has_targeted_taxa ALTER COLUMN id SET DEFAULT nextval('public.has_targeted_taxa_id_seq'::regclass);


--
-- Name: identified_species id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.identified_species ALTER COLUMN id SET DEFAULT nextval('public.identified_species_id_seq'::regclass);


--
-- Name: institution id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.institution ALTER COLUMN id SET DEFAULT nextval('public.institution_id_seq'::regclass);


--
-- Name: internal_biological_material id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_biological_material ALTER COLUMN id SET DEFAULT nextval('public.internal_biological_material_id_seq'::regclass);


--
-- Name: internal_biological_material_is_published_in id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_biological_material_is_published_in ALTER COLUMN id SET DEFAULT nextval('public.internal_biological_material_is_published_in_id_seq'::regclass);


--
-- Name: internal_biological_material_is_treated_by id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_biological_material_is_treated_by ALTER COLUMN id SET DEFAULT nextval('public.internal_biological_material_is_treated_by_id_seq'::regclass);


--
-- Name: internal_sequence id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_sequence ALTER COLUMN id SET DEFAULT nextval('public.internal_sequence_id_seq'::regclass);


--
-- Name: internal_sequence_is_assembled_by id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_sequence_is_assembled_by ALTER COLUMN id SET DEFAULT nextval('public.internal_sequence_is_assembled_by_id_seq'::regclass);


--
-- Name: internal_sequence_is_published_in id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_sequence_is_published_in ALTER COLUMN id SET DEFAULT nextval('public.internal_sequence_is_published_in_id_seq'::regclass);


--
-- Name: motu id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.motu ALTER COLUMN id SET DEFAULT nextval('public.motu_id_seq'::regclass);


--
-- Name: motu_is_generated_by id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.motu_is_generated_by ALTER COLUMN id SET DEFAULT nextval('public.motu_is_generated_by_id_seq'::regclass);


--
-- Name: motu_number id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.motu_number ALTER COLUMN id SET DEFAULT nextval('public.motu_number_id_seq'::regclass);


--
-- Name: municipality id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.municipality ALTER COLUMN id SET DEFAULT nextval('public.municipality_id_seq'::regclass);


--
-- Name: pcr id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pcr ALTER COLUMN id SET DEFAULT nextval('public.pcr_id_seq'::regclass);


--
-- Name: pcr_is_done_by id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pcr_is_done_by ALTER COLUMN id SET DEFAULT nextval('public.pcr_is_done_by_id_seq'::regclass);


--
-- Name: person id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.person ALTER COLUMN id SET DEFAULT nextval('public.person_id_seq'::regclass);


--
-- Name: program id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.program ALTER COLUMN id SET DEFAULT nextval('public.program_id_seq'::regclass);


--
-- Name: sample_is_fixed_with id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sample_is_fixed_with ALTER COLUMN id SET DEFAULT nextval('public.sample_is_fixed_with_id_seq'::regclass);


--
-- Name: sampling id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sampling ALTER COLUMN id SET DEFAULT nextval('public.sampling_id_seq'::regclass);


--
-- Name: sampling_is_done_with_method id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sampling_is_done_with_method ALTER COLUMN id SET DEFAULT nextval('public.sampling_is_done_with_method_id_seq'::regclass);


--
-- Name: sampling_is_funded_by id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sampling_is_funded_by ALTER COLUMN id SET DEFAULT nextval('public.sampling_is_funded_by_id_seq'::regclass);


--
-- Name: sampling_is_performed_by id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sampling_is_performed_by ALTER COLUMN id SET DEFAULT nextval('public.sampling_is_performed_by_id_seq'::regclass);


--
-- Name: site id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.site ALTER COLUMN id SET DEFAULT nextval('public.site_id_seq'::regclass);


--
-- Name: slide_is_mounted_by id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.slide_is_mounted_by ALTER COLUMN id SET DEFAULT nextval('public.slide_is_mounted_by_id_seq'::regclass);


--
-- Name: source id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.source ALTER COLUMN id SET DEFAULT nextval('public.source_id_seq'::regclass);


--
-- Name: source_is_entered_by id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.source_is_entered_by ALTER COLUMN id SET DEFAULT nextval('public.source_is_entered_by_id_seq'::regclass);


--
-- Name: species_is_identified_by id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.species_is_identified_by ALTER COLUMN id SET DEFAULT nextval('public.species_is_identified_by_id_seq'::regclass);


--
-- Name: specimen id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.specimen ALTER COLUMN id SET DEFAULT nextval('public.specimen_id_seq'::regclass);


--
-- Name: specimen_slide id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.specimen_slide ALTER COLUMN id SET DEFAULT nextval('public.specimen_slide_id_seq'::regclass);


--
-- Name: storage_box id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.storage_box ALTER COLUMN id SET DEFAULT nextval('public.storage_box_id_seq'::regclass);


--
-- Name: taxon id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.taxon ALTER COLUMN id SET DEFAULT nextval('public.taxon_id_seq'::regclass);


--
-- Name: user_db id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_db ALTER COLUMN id SET DEFAULT nextval('public.user_db_id_seq'::regclass);


--
-- Name: vocabulary id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vocabulary ALTER COLUMN id SET DEFAULT nextval('public.vocabulary_id_seq'::regclass);


--
-- Name: chromatogram pk_chromatogram; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.chromatogram
    ADD CONSTRAINT pk_chromatogram PRIMARY KEY (id);


--
-- Name: chromatogram_is_processed_to pk_chromatogram_is_processed_to; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.chromatogram_is_processed_to
    ADD CONSTRAINT pk_chromatogram_is_processed_to PRIMARY KEY (id);


--
-- Name: composition_of_internal_biological_material pk_composition_of_internal_biological_material; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.composition_of_internal_biological_material
    ADD CONSTRAINT pk_composition_of_internal_biological_material PRIMARY KEY (id);


--
-- Name: country pk_country; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.country
    ADD CONSTRAINT pk_country PRIMARY KEY (id);


--
-- Name: dna pk_dna; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dna
    ADD CONSTRAINT pk_dna PRIMARY KEY (id);


--
-- Name: dna_is_extracted_by pk_dna_is_extracted_by; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dna_is_extracted_by
    ADD CONSTRAINT pk_dna_is_extracted_by PRIMARY KEY (id);


--
-- Name: external_biological_material pk_external_biological_material; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_biological_material
    ADD CONSTRAINT pk_external_biological_material PRIMARY KEY (id);


--
-- Name: external_biological_material_is_processed_by pk_external_biological_material_is_processed_by; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_biological_material_is_processed_by
    ADD CONSTRAINT pk_external_biological_material_is_processed_by PRIMARY KEY (id);


--
-- Name: external_biological_material_is_published_in pk_external_biological_material_is_published_in; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_biological_material_is_published_in
    ADD CONSTRAINT pk_external_biological_material_is_published_in PRIMARY KEY (id);


--
-- Name: external_sequence pk_external_sequence; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_sequence
    ADD CONSTRAINT pk_external_sequence PRIMARY KEY (id);


--
-- Name: external_sequence_is_entered_by pk_external_sequence_is_entered_by; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_sequence_is_entered_by
    ADD CONSTRAINT pk_external_sequence_is_entered_by PRIMARY KEY (id);


--
-- Name: external_sequence_is_published_in pk_external_sequence_is_published_in; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_sequence_is_published_in
    ADD CONSTRAINT pk_external_sequence_is_published_in PRIMARY KEY (id);


--
-- Name: has_targeted_taxa pk_has_targeted_taxa; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.has_targeted_taxa
    ADD CONSTRAINT pk_has_targeted_taxa PRIMARY KEY (id);


--
-- Name: identified_species pk_identified_species; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.identified_species
    ADD CONSTRAINT pk_identified_species PRIMARY KEY (id);


--
-- Name: institution pk_institution; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.institution
    ADD CONSTRAINT pk_institution PRIMARY KEY (id);


--
-- Name: internal_biological_material pk_internal_biological_material; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_biological_material
    ADD CONSTRAINT pk_internal_biological_material PRIMARY KEY (id);


--
-- Name: internal_biological_material_is_published_in pk_internal_biological_material_is_published_in; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_biological_material_is_published_in
    ADD CONSTRAINT pk_internal_biological_material_is_published_in PRIMARY KEY (id);


--
-- Name: internal_biological_material_is_treated_by pk_internal_biological_material_is_treated_by; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_biological_material_is_treated_by
    ADD CONSTRAINT pk_internal_biological_material_is_treated_by PRIMARY KEY (id);


--
-- Name: internal_sequence pk_internal_sequence; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_sequence
    ADD CONSTRAINT pk_internal_sequence PRIMARY KEY (id);


--
-- Name: internal_sequence_is_assembled_by pk_internal_sequence_is_assembled_by; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_sequence_is_assembled_by
    ADD CONSTRAINT pk_internal_sequence_is_assembled_by PRIMARY KEY (id);


--
-- Name: internal_sequence_is_published_in pk_internal_sequence_is_published_in; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_sequence_is_published_in
    ADD CONSTRAINT pk_internal_sequence_is_published_in PRIMARY KEY (id);


--
-- Name: motu pk_motu; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.motu
    ADD CONSTRAINT pk_motu PRIMARY KEY (id);


--
-- Name: motu_is_generated_by pk_motu_is_generated_by; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.motu_is_generated_by
    ADD CONSTRAINT pk_motu_is_generated_by PRIMARY KEY (id);


--
-- Name: motu_number pk_motu_number; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.motu_number
    ADD CONSTRAINT pk_motu_number PRIMARY KEY (id);


--
-- Name: municipality pk_municipality; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.municipality
    ADD CONSTRAINT pk_municipality PRIMARY KEY (id);


--
-- Name: pcr pk_pcr; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pcr
    ADD CONSTRAINT pk_pcr PRIMARY KEY (id);


--
-- Name: pcr_is_done_by pk_pcr_is_done_by; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pcr_is_done_by
    ADD CONSTRAINT pk_pcr_is_done_by PRIMARY KEY (id);


--
-- Name: person pk_person; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.person
    ADD CONSTRAINT pk_person PRIMARY KEY (id);


--
-- Name: program pk_program; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.program
    ADD CONSTRAINT pk_program PRIMARY KEY (id);


--
-- Name: sample_is_fixed_with pk_sample_is_fixed_with; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sample_is_fixed_with
    ADD CONSTRAINT pk_sample_is_fixed_with PRIMARY KEY (id);


--
-- Name: sampling pk_sampling; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sampling
    ADD CONSTRAINT pk_sampling PRIMARY KEY (id);


--
-- Name: sampling_is_done_with_method pk_sampling_is_done_with_method; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sampling_is_done_with_method
    ADD CONSTRAINT pk_sampling_is_done_with_method PRIMARY KEY (id);


--
-- Name: sampling_is_funded_by pk_sampling_is_funded_by; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sampling_is_funded_by
    ADD CONSTRAINT pk_sampling_is_funded_by PRIMARY KEY (id);


--
-- Name: sampling_is_performed_by pk_sampling_is_performed_by; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sampling_is_performed_by
    ADD CONSTRAINT pk_sampling_is_performed_by PRIMARY KEY (id);


--
-- Name: site pk_site; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.site
    ADD CONSTRAINT pk_site PRIMARY KEY (id);


--
-- Name: slide_is_mounted_by pk_slide_is_mounted_by; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.slide_is_mounted_by
    ADD CONSTRAINT pk_slide_is_mounted_by PRIMARY KEY (id);


--
-- Name: source pk_source; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.source
    ADD CONSTRAINT pk_source PRIMARY KEY (id);


--
-- Name: source_is_entered_by pk_source_is_entered_by; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.source_is_entered_by
    ADD CONSTRAINT pk_source_is_entered_by PRIMARY KEY (id);


--
-- Name: species_is_identified_by pk_species_is_identified_by; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.species_is_identified_by
    ADD CONSTRAINT pk_species_is_identified_by PRIMARY KEY (id);


--
-- Name: specimen pk_specimen; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.specimen
    ADD CONSTRAINT pk_specimen PRIMARY KEY (id);


--
-- Name: specimen_slide pk_specimen_slide; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.specimen_slide
    ADD CONSTRAINT pk_specimen_slide PRIMARY KEY (id);


--
-- Name: storage_box pk_storage_box; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.storage_box
    ADD CONSTRAINT pk_storage_box PRIMARY KEY (id);


--
-- Name: taxon pk_taxon; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.taxon
    ADD CONSTRAINT pk_taxon PRIMARY KEY (id);


--
-- Name: user_db pk_user_db; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_db
    ADD CONSTRAINT pk_user_db PRIMARY KEY (id);


--
-- Name: vocabulary pk_vocabulary; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vocabulary
    ADD CONSTRAINT pk_vocabulary PRIMARY KEY (id);


--
-- Name: chromatogram uk_chromatogram__chromatogram_code; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.chromatogram
    ADD CONSTRAINT uk_chromatogram__chromatogram_code UNIQUE (chromatogram_code);


--
-- Name: country uk_country__country_code; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.country
    ADD CONSTRAINT uk_country__country_code UNIQUE (country_code);


--
-- Name: dna uk_dna__dna_code; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dna
    ADD CONSTRAINT uk_dna__dna_code UNIQUE (dna_code);


--
-- Name: external_biological_material uk_external_biological_material__external_biological_material_c; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_biological_material
    ADD CONSTRAINT uk_external_biological_material__external_biological_material_c UNIQUE (external_biological_material_code);


--
-- Name: external_sequence uk_external_sequence__external_sequence_alignment_code; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_sequence
    ADD CONSTRAINT uk_external_sequence__external_sequence_alignment_code UNIQUE (external_sequence_alignment_code);


--
-- Name: external_sequence uk_external_sequence__external_sequence_code; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_sequence
    ADD CONSTRAINT uk_external_sequence__external_sequence_code UNIQUE (external_sequence_code);


--
-- Name: institution uk_institution__institution_name; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.institution
    ADD CONSTRAINT uk_institution__institution_name UNIQUE (institution_name);


--
-- Name: internal_biological_material uk_internal_biological_material__internal_biological_material_c; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_biological_material
    ADD CONSTRAINT uk_internal_biological_material__internal_biological_material_c UNIQUE (internal_biological_material_code);


--
-- Name: internal_sequence uk_internal_sequence__internal_sequence_alignment_code; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_sequence
    ADD CONSTRAINT uk_internal_sequence__internal_sequence_alignment_code UNIQUE (internal_sequence_alignment_code);


--
-- Name: internal_sequence uk_internal_sequence__internal_sequence_code; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_sequence
    ADD CONSTRAINT uk_internal_sequence__internal_sequence_code UNIQUE (internal_sequence_code);


--
-- Name: municipality uk_municipality__municipality_code; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.municipality
    ADD CONSTRAINT uk_municipality__municipality_code UNIQUE (municipality_code);


--
-- Name: pcr uk_pcr__pcr_code; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pcr
    ADD CONSTRAINT uk_pcr__pcr_code UNIQUE (pcr_code);


--
-- Name: person uk_person__person_name; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.person
    ADD CONSTRAINT uk_person__person_name UNIQUE (person_name);


--
-- Name: program uk_program__program_code; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.program
    ADD CONSTRAINT uk_program__program_code UNIQUE (program_code);


--
-- Name: sampling uk_sampling__sample_code; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sampling
    ADD CONSTRAINT uk_sampling__sample_code UNIQUE (sample_code);


--
-- Name: site uk_site__site_code; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.site
    ADD CONSTRAINT uk_site__site_code UNIQUE (site_code);


--
-- Name: source uk_source__source_code; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.source
    ADD CONSTRAINT uk_source__source_code UNIQUE (source_code);


--
-- Name: specimen uk_specimen__specimen_molecular_code; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.specimen
    ADD CONSTRAINT uk_specimen__specimen_molecular_code UNIQUE (specimen_molecular_code);


--
-- Name: specimen uk_specimen__specimen_morphological_code; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.specimen
    ADD CONSTRAINT uk_specimen__specimen_morphological_code UNIQUE (specimen_morphological_code);


--
-- Name: specimen_slide uk_specimen_slide__collection_slide_code; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.specimen_slide
    ADD CONSTRAINT uk_specimen_slide__collection_slide_code UNIQUE (collection_slide_code);


--
-- Name: storage_box uk_storage_box__box_code; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.storage_box
    ADD CONSTRAINT uk_storage_box__box_code UNIQUE (box_code);


--
-- Name: taxon uk_taxon__taxon_code; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.taxon
    ADD CONSTRAINT uk_taxon__taxon_code UNIQUE (taxon_code);


--
-- Name: taxon uk_taxon__taxon_name; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.taxon
    ADD CONSTRAINT uk_taxon__taxon_name UNIQUE (taxon_name);


--
-- Name: user_db uk_user_db__username; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_db
    ADD CONSTRAINT uk_user_db__username UNIQUE (user_name);


--
-- Name: vocabulary uk_vocabulary__parent__code; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vocabulary
    ADD CONSTRAINT uk_vocabulary__parent__code UNIQUE (code, parent);


--
-- Name: idx_1041853b2b63d494; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_1041853b2b63d494 ON public.pcr_is_done_by USING btree (pcr_fk);


--
-- Name: idx_1041853bb53cd04c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_1041853bb53cd04c ON public.pcr_is_done_by USING btree (person_fk);


--
-- Name: idx_10a697444236d33e; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_10a697444236d33e ON public.composition_of_internal_biological_material USING btree (specimen_type_voc_fk);


--
-- Name: idx_10a6974454dbbd4d; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_10a6974454dbbd4d ON public.composition_of_internal_biological_material USING btree (internal_biological_material_fk);


--
-- Name: idx_16dc6005821b1d3f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_16dc6005821b1d3f ON public.source_is_entered_by USING btree (source_fk);


--
-- Name: idx_16dc6005b53cd04c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_16dc6005b53cd04c ON public.source_is_entered_by USING btree (person_fk);


--
-- Name: idx_17a90ea3503b4409; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_17a90ea3503b4409 ON public.motu_is_generated_by USING btree (motu_fk);


--
-- Name: idx_17a90ea3b53cd04c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_17a90ea3b53cd04c ON public.motu_is_generated_by USING btree (person_fk);


--
-- Name: idx_18fcbb8f662d9b98; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_18fcbb8f662d9b98 ON public.sampling_is_funded_by USING btree (sampling_fk);


--
-- Name: idx_18fcbb8f759c7bb0; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_18fcbb8f759c7bb0 ON public.sampling_is_funded_by USING btree (program_fk);


--
-- Name: idx_1dcf9af9c53b46b; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_1dcf9af9c53b46b ON public.dna USING btree (dna_quality_voc_fk);


--
-- Name: idx_353cf66988085e0f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_353cf66988085e0f ON public.internal_sequence USING btree (internal_sequence_status_voc_fk);


--
-- Name: idx_353cf669a30c442f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_353cf669a30c442f ON public.internal_sequence USING btree (date_precision_voc_fk);


--
-- Name: idx_49d19c8d40d80ecd; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_49d19c8d40d80ecd ON public.identified_species USING btree (external_biological_material_fk);


--
-- Name: idx_49d19c8d54dbbd4d; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_49d19c8d54dbbd4d ON public.identified_species USING btree (internal_biological_material_fk);


--
-- Name: idx_49d19c8d5be90e48; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_49d19c8d5be90e48 ON public.identified_species USING btree (internal_sequence_fk);


--
-- Name: idx_49d19c8d5f2c6176; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_49d19c8d5f2c6176 ON public.identified_species USING btree (specimen_fk);


--
-- Name: idx_49d19c8d7b09e3bc; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_49d19c8d7b09e3bc ON public.identified_species USING btree (taxon_fk);


--
-- Name: idx_49d19c8da30c442f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_49d19c8da30c442f ON public.identified_species USING btree (date_precision_voc_fk);


--
-- Name: idx_49d19c8dcdd1f756; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_49d19c8dcdd1f756 ON public.identified_species USING btree (external_sequence_fk);


--
-- Name: idx_49d19c8dfb5f790; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_49d19c8dfb5f790 ON public.identified_species USING btree (identification_criterion_voc_fk);


--
-- Name: idx_4e79cb8d40e7e0b3; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_4e79cb8d40e7e0b3 ON public.motu_number USING btree (delimitation_method_voc_fk);


--
-- Name: idx_4e79cb8d503b4409; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_4e79cb8d503b4409 ON public.motu_number USING btree (motu_fk);


--
-- Name: idx_4e79cb8d5be90e48; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_4e79cb8d5be90e48 ON public.motu_number USING btree (internal_sequence_fk);


--
-- Name: idx_4e79cb8dcdd1f756; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_4e79cb8dcdd1f756 ON public.motu_number USING btree (external_sequence_fk);


--
-- Name: idx_55ae4a3d369ab36b; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_55ae4a3d369ab36b ON public.sampling USING btree (site_fk);


--
-- Name: idx_55ae4a3d50bb334e; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_55ae4a3d50bb334e ON public.sampling USING btree (donation_voc_fk);


--
-- Name: idx_55ae4a3da30c442f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_55ae4a3da30c442f ON public.sampling USING btree (date_precision_voc_fk);


--
-- Name: idx_5a6bd88a29b38195; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5a6bd88a29b38195 ON public.sampling_is_done_with_method USING btree (sampling_method_voc_fk);


--
-- Name: idx_5a6bd88a662d9b98; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5a6bd88a662d9b98 ON public.sampling_is_done_with_method USING btree (sampling_fk);


--
-- Name: idx_5b6b99362c5b04a7; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5b6b99362c5b04a7 ON public.pcr USING btree (forward_primer_voc_fk);


--
-- Name: idx_5b6b99364b06319d; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5b6b99364b06319d ON public.pcr USING btree (dna_fk);


--
-- Name: idx_5b6b99366ccc2566; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5b6b99366ccc2566 ON public.pcr USING btree (pcr_specificity_voc_fk);


--
-- Name: idx_5b6b99368b4a1710; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5b6b99368b4a1710 ON public.pcr USING btree (pcr_quality_voc_fk);


--
-- Name: idx_5b6b99369d3cdb05; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5b6b99369d3cdb05 ON public.pcr USING btree (gene_voc_fk);


--
-- Name: idx_5b6b9936a30c442f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5b6b9936a30c442f ON public.pcr USING btree (date_precision_voc_fk);


--
-- Name: idx_5b6b9936f1694267; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5b6b9936f1694267 ON public.pcr USING btree (reverse_primer_voc_fk);


--
-- Name: idx_5ee42fce4236d33e; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5ee42fce4236d33e ON public.specimen USING btree (specimen_type_voc_fk);


--
-- Name: idx_5ee42fce54dbbd4d; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5ee42fce54dbbd4d ON public.specimen USING btree (internal_biological_material_fk);


--
-- Name: idx_60129a315fd841ac; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_60129a315fd841ac ON public.sample_is_fixed_with USING btree (fixative_voc_fk);


--
-- Name: idx_60129a31662d9b98; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_60129a31662d9b98 ON public.sample_is_fixed_with USING btree (sampling_fk);


--
-- Name: idx_69c58aff54dbbd4d; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_69c58aff54dbbd4d ON public.internal_biological_material_is_treated_by USING btree (internal_biological_material_fk);


--
-- Name: idx_69c58affb53cd04c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_69c58affb53cd04c ON public.internal_biological_material_is_treated_by USING btree (person_fk);


--
-- Name: idx_7718edef41a72d48; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_7718edef41a72d48 ON public.storage_box USING btree (collection_code_voc_fk);


--
-- Name: idx_7718edef57552d30; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_7718edef57552d30 ON public.storage_box USING btree (box_type_voc_fk);


--
-- Name: idx_7718edef9e7b0e1f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_7718edef9e7b0e1f ON public.storage_box USING btree (collection_type_voc_fk);


--
-- Name: idx_7d78636f40d80ecd; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_7d78636f40d80ecd ON public.external_biological_material_is_processed_by USING btree (external_biological_material_fk);


--
-- Name: idx_7d78636fb53cd04c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_7d78636fb53cd04c ON public.external_biological_material_is_processed_by USING btree (person_fk);


--
-- Name: idx_801c3911b669f53d; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_801c3911b669f53d ON public.identified_species USING btree (type_material_voc_fk);


--
-- Name: idx_88295540b53cd04c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_88295540b53cd04c ON public.slide_is_mounted_by USING btree (person_fk);


--
-- Name: idx_88295540d9c85992; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_88295540d9c85992 ON public.slide_is_mounted_by USING btree (specimen_slide_fk);


--
-- Name: idx_8d0e8d6a821b1d3f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_8d0e8d6a821b1d3f ON public.external_sequence_is_published_in USING btree (source_fk);


--
-- Name: idx_8d0e8d6acdd1f756; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_8d0e8d6acdd1f756 ON public.external_sequence_is_published_in USING btree (external_sequence_fk);


--
-- Name: idx_8da827e22b644673; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_8da827e22b644673 ON public.specimen_slide USING btree (storage_box_fk);


--
-- Name: idx_8da827e25f2c6176; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_8da827e25f2c6176 ON public.specimen_slide USING btree (specimen_fk);


--
-- Name: idx_8da827e2a30c442f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_8da827e2a30c442f ON public.specimen_slide USING btree (date_precision_voc_fk);


--
-- Name: idx_9e9f85cf514d78e0; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9e9f85cf514d78e0 ON public.external_sequence USING btree (external_sequence_origin_voc_fk);


--
-- Name: idx_9e9f85cf662d9b98; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9e9f85cf662d9b98 ON public.external_sequence USING btree (sampling_fk);


--
-- Name: idx_9e9f85cf88085e0f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9e9f85cf88085e0f ON public.external_sequence USING btree (external_sequence_status_voc_fk);


--
-- Name: idx_9e9f85cf9d3cdb05; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9e9f85cf9d3cdb05 ON public.external_sequence USING btree (gene_voc_fk);


--
-- Name: idx_9e9f85cfa30c442f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9e9f85cfa30c442f ON public.external_sequence USING btree (date_precision_voc_fk);


--
-- Name: idx_9f39f8b143d4e2c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9f39f8b143d4e2c ON public.site USING btree (municipality_fk);


--
-- Name: idx_9f39f8b14d50d031; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9f39f8b14d50d031 ON public.site USING btree (access_point_voc_fk);


--
-- Name: idx_9f39f8b1b1c3431a; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9f39f8b1b1c3431a ON public.site USING btree (country_fk);


--
-- Name: idx_9f39f8b1c23046ae; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9f39f8b1c23046ae ON public.site USING btree (habitat_type_voc_fk);


--
-- Name: idx_9f39f8b1e86dbd90; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9f39f8b1e86dbd90 ON public.site USING btree (coordinate_precision_voc_fk);


--
-- Name: idx_b786c5214b06319d; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_b786c5214b06319d ON public.dna_is_extracted_by USING btree (dna_fk);


--
-- Name: idx_b786c521b53cd04c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_b786c521b53cd04c ON public.dna_is_extracted_by USING btree (person_fk);


--
-- Name: idx_ba1841a52b644673; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_ba1841a52b644673 ON public.internal_biological_material USING btree (storage_box_fk);


--
-- Name: idx_ba1841a5662d9b98; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_ba1841a5662d9b98 ON public.internal_biological_material USING btree (sampling_fk);


--
-- Name: idx_ba1841a5a30c442f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_ba1841a5a30c442f ON public.internal_biological_material USING btree (date_precision_voc_fk);


--
-- Name: idx_ba1841a5a897cc9e; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_ba1841a5a897cc9e ON public.internal_biological_material USING btree (eyes_voc_fk);


--
-- Name: idx_ba1841a5b0b56b73; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_ba1841a5b0b56b73 ON public.internal_biological_material USING btree (pigmentation_voc_fk);


--
-- Name: idx_ba97b9c45be90e48; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_ba97b9c45be90e48 ON public.internal_sequence_is_published_in USING btree (internal_sequence_fk);


--
-- Name: idx_ba97b9c4821b1d3f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_ba97b9c4821b1d3f ON public.internal_sequence_is_published_in USING btree (source_fk);


--
-- Name: idx_bd45639e5be90e48; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_bd45639e5be90e48 ON public.chromatogram_is_processed_to USING btree (internal_sequence_fk);


--
-- Name: idx_bd45639eefcfd332; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_bd45639eefcfd332 ON public.chromatogram_is_processed_to USING btree (chromatogram_fk);


--
-- Name: idx_c0df0ce4662d9b98; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_c0df0ce4662d9b98 ON public.has_targeted_taxa USING btree (sampling_fk);


--
-- Name: idx_c0df0ce47b09e3bc; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_c0df0ce47b09e3bc ON public.has_targeted_taxa USING btree (taxon_fk);


--
-- Name: idx_d2338bb240d80ecd; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_d2338bb240d80ecd ON public.external_biological_material_is_published_in USING btree (external_biological_material_fk);


--
-- Name: idx_d2338bb2821b1d3f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_d2338bb2821b1d3f ON public.external_biological_material_is_published_in USING btree (source_fk);


--
-- Name: idx_dc41e25ab53cd04c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_dc41e25ab53cd04c ON public.external_sequence_is_entered_by USING btree (person_fk);


--
-- Name: idx_dc41e25acdd1f756; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_dc41e25acdd1f756 ON public.external_sequence_is_entered_by USING btree (external_sequence_fk);


--
-- Name: idx_dna__date_precision_voc_fk; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_dna__date_precision_voc_fk ON public.dna USING btree (date_precision_voc_fk);


--
-- Name: idx_dna__dna_extraction_method_voc_fk; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_dna__dna_extraction_method_voc_fk ON public.dna USING btree (dna_extraction_method_voc_fk);


--
-- Name: idx_dna__specimen_fk; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_dna__specimen_fk ON public.dna USING btree (specimen_fk);


--
-- Name: idx_dna__storage_box_fk; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_dna__storage_box_fk ON public.dna USING btree (storage_box_fk);


--
-- Name: idx_e2e2d1eeb1c3431a; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_e2e2d1eeb1c3431a ON public.municipality USING btree (country_fk);


--
-- Name: idx_ea07bfa754dbbd4d; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_ea07bfa754dbbd4d ON public.internal_biological_material_is_published_in USING btree (internal_biological_material_fk);


--
-- Name: idx_ea07bfa7821b1d3f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_ea07bfa7821b1d3f ON public.internal_biological_material_is_published_in USING btree (source_fk);


--
-- Name: idx_ee2a88c9662d9b98; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_ee2a88c9662d9b98 ON public.sampling_is_performed_by USING btree (sampling_fk);


--
-- Name: idx_ee2a88c9b53cd04c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_ee2a88c9b53cd04c ON public.sampling_is_performed_by USING btree (person_fk);


--
-- Name: idx_eefa43f3662d9b98; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_eefa43f3662d9b98 ON public.external_biological_material USING btree (sampling_fk);


--
-- Name: idx_eefa43f382acdc4; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_eefa43f382acdc4 ON public.external_biological_material USING btree (number_of_specimens_voc_fk);


--
-- Name: idx_eefa43f3a30c442f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_eefa43f3a30c442f ON public.external_biological_material USING btree (date_precision_voc_fk);


--
-- Name: idx_eefa43f3a897cc9e; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_eefa43f3a897cc9e ON public.external_biological_material USING btree (eyes_voc_fk);


--
-- Name: idx_eefa43f3b0b56b73; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_eefa43f3b0b56b73 ON public.external_biological_material USING btree (pigmentation_voc_fk);


--
-- Name: idx_f6971ba85be90e48; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_f6971ba85be90e48 ON public.internal_sequence_is_assembled_by USING btree (internal_sequence_fk);


--
-- Name: idx_f6971ba8b53cd04c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_f6971ba8b53cd04c ON public.internal_sequence_is_assembled_by USING btree (person_fk);


--
-- Name: idx_f8fccf63b4ab6ba0; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_f8fccf63b4ab6ba0 ON public.species_is_identified_by USING btree (identified_species_fk);


--
-- Name: idx_f8fccf63b53cd04c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_f8fccf63b53cd04c ON public.species_is_identified_by USING btree (person_fk);


--
-- Name: idx_fcb2dab7206fe5c0; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_fcb2dab7206fe5c0 ON public.chromatogram USING btree (chromato_quality_voc_fk);


--
-- Name: idx_fcb2dab7286bbca9; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_fcb2dab7286bbca9 ON public.chromatogram USING btree (chromato_primer_voc_fk);


--
-- Name: idx_fcb2dab72b63d494; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_fcb2dab72b63d494 ON public.chromatogram USING btree (pcr_fk);


--
-- Name: idx_fcb2dab7e8441376; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_fcb2dab7e8441376 ON public.chromatogram USING btree (institution_fk);


--
-- Name: idx_fcec9efe8441376; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_fcec9efe8441376 ON public.person USING btree (institution_fk);


--
-- Name: internal_sequence_is_published_in fk_; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_sequence_is_published_in
    ADD CONSTRAINT fk_ FOREIGN KEY (source_fk) REFERENCES public.source(id) ON DELETE CASCADE;


--
-- Name: internal_sequence_is_published_in fk_1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_sequence_is_published_in
    ADD CONSTRAINT fk_1 FOREIGN KEY (internal_sequence_fk) REFERENCES public.internal_sequence(id) ON DELETE CASCADE;


--
-- Name: species_is_identified_by fk_10; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.species_is_identified_by
    ADD CONSTRAINT fk_10 FOREIGN KEY (person_fk) REFERENCES public.person(id);


--
-- Name: sampling_is_funded_by fk_100; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sampling_is_funded_by
    ADD CONSTRAINT fk_100 FOREIGN KEY (program_fk) REFERENCES public.program(id);


--
-- Name: sampling_is_funded_by fk_101; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sampling_is_funded_by
    ADD CONSTRAINT fk_101 FOREIGN KEY (sampling_fk) REFERENCES public.sampling(id) ON DELETE CASCADE;


--
-- Name: specimen_slide fk_102; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.specimen_slide
    ADD CONSTRAINT fk_102 FOREIGN KEY (date_precision_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: specimen_slide fk_103; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.specimen_slide
    ADD CONSTRAINT fk_103 FOREIGN KEY (storage_box_fk) REFERENCES public.storage_box(id);


--
-- Name: specimen_slide fk_104; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.specimen_slide
    ADD CONSTRAINT fk_104 FOREIGN KEY (specimen_fk) REFERENCES public.specimen(id);


--
-- Name: motu_number fk_11; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.motu_number
    ADD CONSTRAINT fk_11 FOREIGN KEY (external_sequence_fk) REFERENCES public.external_sequence(id);


--
-- Name: motu_number fk_12; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.motu_number
    ADD CONSTRAINT fk_12 FOREIGN KEY (delimitation_method_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: motu_number fk_13; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.motu_number
    ADD CONSTRAINT fk_13 FOREIGN KEY (internal_sequence_fk) REFERENCES public.internal_sequence(id);


--
-- Name: motu_number fk_14; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.motu_number
    ADD CONSTRAINT fk_14 FOREIGN KEY (motu_fk) REFERENCES public.motu(id) ON DELETE CASCADE;


--
-- Name: composition_of_internal_biological_material fk_15; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.composition_of_internal_biological_material
    ADD CONSTRAINT fk_15 FOREIGN KEY (specimen_type_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: composition_of_internal_biological_material fk_16; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.composition_of_internal_biological_material
    ADD CONSTRAINT fk_16 FOREIGN KEY (internal_biological_material_fk) REFERENCES public.internal_biological_material(id) ON DELETE CASCADE;


--
-- Name: has_targeted_taxa fk_17; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.has_targeted_taxa
    ADD CONSTRAINT fk_17 FOREIGN KEY (sampling_fk) REFERENCES public.sampling(id) ON DELETE CASCADE;


--
-- Name: has_targeted_taxa fk_18; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.has_targeted_taxa
    ADD CONSTRAINT fk_18 FOREIGN KEY (taxon_fk) REFERENCES public.taxon(id);


--
-- Name: internal_sequence_is_assembled_by fk_19; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_sequence_is_assembled_by
    ADD CONSTRAINT fk_19 FOREIGN KEY (internal_sequence_fk) REFERENCES public.internal_sequence(id) ON DELETE CASCADE;


--
-- Name: external_sequence fk_2; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_sequence
    ADD CONSTRAINT fk_2 FOREIGN KEY (gene_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: internal_sequence_is_assembled_by fk_20; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_sequence_is_assembled_by
    ADD CONSTRAINT fk_20 FOREIGN KEY (person_fk) REFERENCES public.person(id);


--
-- Name: pcr_is_done_by fk_21; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pcr_is_done_by
    ADD CONSTRAINT fk_21 FOREIGN KEY (pcr_fk) REFERENCES public.pcr(id) ON DELETE CASCADE;


--
-- Name: pcr_is_done_by fk_22; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pcr_is_done_by
    ADD CONSTRAINT fk_22 FOREIGN KEY (person_fk) REFERENCES public.person(id);


--
-- Name: dna_is_extracted_by fk_23; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dna_is_extracted_by
    ADD CONSTRAINT fk_23 FOREIGN KEY (dna_fk) REFERENCES public.dna(id) ON DELETE CASCADE;


--
-- Name: dna_is_extracted_by fk_24; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dna_is_extracted_by
    ADD CONSTRAINT fk_24 FOREIGN KEY (person_fk) REFERENCES public.person(id);


--
-- Name: slide_is_mounted_by fk_25; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.slide_is_mounted_by
    ADD CONSTRAINT fk_25 FOREIGN KEY (specimen_slide_fk) REFERENCES public.specimen_slide(id) ON DELETE CASCADE;


--
-- Name: slide_is_mounted_by fk_26; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.slide_is_mounted_by
    ADD CONSTRAINT fk_26 FOREIGN KEY (person_fk) REFERENCES public.person(id);


--
-- Name: internal_biological_material_is_treated_by fk_27; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_biological_material_is_treated_by
    ADD CONSTRAINT fk_27 FOREIGN KEY (internal_biological_material_fk) REFERENCES public.internal_biological_material(id) ON DELETE CASCADE;


--
-- Name: internal_biological_material_is_treated_by fk_28; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_biological_material_is_treated_by
    ADD CONSTRAINT fk_28 FOREIGN KEY (person_fk) REFERENCES public.person(id);


--
-- Name: source_is_entered_by fk_29; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.source_is_entered_by
    ADD CONSTRAINT fk_29 FOREIGN KEY (source_fk) REFERENCES public.source(id) ON DELETE CASCADE;


--
-- Name: external_sequence fk_3; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_sequence
    ADD CONSTRAINT fk_3 FOREIGN KEY (date_precision_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: source_is_entered_by fk_30; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.source_is_entered_by
    ADD CONSTRAINT fk_30 FOREIGN KEY (person_fk) REFERENCES public.person(id);


--
-- Name: external_sequence_is_entered_by fk_31; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_sequence_is_entered_by
    ADD CONSTRAINT fk_31 FOREIGN KEY (external_sequence_fk) REFERENCES public.external_sequence(id) ON DELETE CASCADE;


--
-- Name: external_sequence_is_entered_by fk_32; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_sequence_is_entered_by
    ADD CONSTRAINT fk_32 FOREIGN KEY (person_fk) REFERENCES public.person(id);


--
-- Name: motu_is_generated_by fk_33; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.motu_is_generated_by
    ADD CONSTRAINT fk_33 FOREIGN KEY (motu_fk) REFERENCES public.motu(id) ON DELETE CASCADE;


--
-- Name: motu_is_generated_by fk_34; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.motu_is_generated_by
    ADD CONSTRAINT fk_34 FOREIGN KEY (person_fk) REFERENCES public.person(id);


--
-- Name: internal_sequence fk_35; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_sequence
    ADD CONSTRAINT fk_35 FOREIGN KEY (date_precision_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: internal_sequence fk_36; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_sequence
    ADD CONSTRAINT fk_36 FOREIGN KEY (internal_sequence_status_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: chromatogram_is_processed_to fk_37; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.chromatogram_is_processed_to
    ADD CONSTRAINT fk_37 FOREIGN KEY (chromatogram_fk) REFERENCES public.chromatogram(id);


--
-- Name: chromatogram_is_processed_to fk_38; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.chromatogram_is_processed_to
    ADD CONSTRAINT fk_38 FOREIGN KEY (internal_sequence_fk) REFERENCES public.internal_sequence(id) ON DELETE CASCADE;


--
-- Name: municipality fk_39; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.municipality
    ADD CONSTRAINT fk_39 FOREIGN KEY (country_fk) REFERENCES public.country(id);


--
-- Name: external_sequence fk_4; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_sequence
    ADD CONSTRAINT fk_4 FOREIGN KEY (external_sequence_origin_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: sampling_is_done_with_method fk_40; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sampling_is_done_with_method
    ADD CONSTRAINT fk_40 FOREIGN KEY (sampling_method_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: sampling_is_done_with_method fk_41; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sampling_is_done_with_method
    ADD CONSTRAINT fk_41 FOREIGN KEY (sampling_fk) REFERENCES public.sampling(id) ON DELETE CASCADE;


--
-- Name: sample_is_fixed_with fk_42; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sample_is_fixed_with
    ADD CONSTRAINT fk_42 FOREIGN KEY (fixative_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: sample_is_fixed_with fk_43; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sample_is_fixed_with
    ADD CONSTRAINT fk_43 FOREIGN KEY (sampling_fk) REFERENCES public.sampling(id) ON DELETE CASCADE;


--
-- Name: internal_biological_material_is_published_in fk_44; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_biological_material_is_published_in
    ADD CONSTRAINT fk_44 FOREIGN KEY (internal_biological_material_fk) REFERENCES public.internal_biological_material(id) ON DELETE CASCADE;


--
-- Name: internal_biological_material_is_published_in fk_45; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_biological_material_is_published_in
    ADD CONSTRAINT fk_45 FOREIGN KEY (source_fk) REFERENCES public.source(id) ON DELETE CASCADE;


--
-- Name: external_biological_material fk_46; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_biological_material
    ADD CONSTRAINT fk_46 FOREIGN KEY (sampling_fk) REFERENCES public.sampling(id);


--
-- Name: external_biological_material fk_47; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_biological_material
    ADD CONSTRAINT fk_47 FOREIGN KEY (date_precision_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: external_biological_material fk_48; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_biological_material
    ADD CONSTRAINT fk_48 FOREIGN KEY (number_of_specimens_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: external_biological_material fk_49; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_biological_material
    ADD CONSTRAINT fk_49 FOREIGN KEY (pigmentation_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: external_sequence fk_5; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_sequence
    ADD CONSTRAINT fk_5 FOREIGN KEY (sampling_fk) REFERENCES public.sampling(id);


--
-- Name: external_biological_material fk_50; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_biological_material
    ADD CONSTRAINT fk_50 FOREIGN KEY (eyes_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: external_biological_material_is_processed_by fk_51; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_biological_material_is_processed_by
    ADD CONSTRAINT fk_51 FOREIGN KEY (person_fk) REFERENCES public.person(id);


--
-- Name: external_biological_material_is_processed_by fk_52; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_biological_material_is_processed_by
    ADD CONSTRAINT fk_52 FOREIGN KEY (external_biological_material_fk) REFERENCES public.external_biological_material(id) ON DELETE CASCADE;


--
-- Name: external_biological_material_is_published_in fk_53; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_biological_material_is_published_in
    ADD CONSTRAINT fk_53 FOREIGN KEY (external_biological_material_fk) REFERENCES public.external_biological_material(id) ON DELETE CASCADE;


--
-- Name: external_biological_material_is_published_in fk_54; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_biological_material_is_published_in
    ADD CONSTRAINT fk_54 FOREIGN KEY (source_fk) REFERENCES public.source(id) ON DELETE CASCADE;


--
-- Name: site fk_55; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.site
    ADD CONSTRAINT fk_55 FOREIGN KEY (municipality_fk) REFERENCES public.municipality(id);


--
-- Name: site fk_56; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.site
    ADD CONSTRAINT fk_56 FOREIGN KEY (country_fk) REFERENCES public.country(id);


--
-- Name: site fk_57; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.site
    ADD CONSTRAINT fk_57 FOREIGN KEY (access_point_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: site fk_58; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.site
    ADD CONSTRAINT fk_58 FOREIGN KEY (habitat_type_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: site fk_59; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.site
    ADD CONSTRAINT fk_59 FOREIGN KEY (coordinate_precision_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: external_sequence fk_6; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_sequence
    ADD CONSTRAINT fk_6 FOREIGN KEY (external_sequence_status_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: sampling fk_60; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sampling
    ADD CONSTRAINT fk_60 FOREIGN KEY (date_precision_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: sampling fk_61; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sampling
    ADD CONSTRAINT fk_61 FOREIGN KEY (donation_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: sampling fk_62; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sampling
    ADD CONSTRAINT fk_62 FOREIGN KEY (site_fk) REFERENCES public.site(id);


--
-- Name: person fk_63; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.person
    ADD CONSTRAINT fk_63 FOREIGN KEY (institution_fk) REFERENCES public.institution(id);


--
-- Name: sampling_is_performed_by fk_64; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sampling_is_performed_by
    ADD CONSTRAINT fk_64 FOREIGN KEY (person_fk) REFERENCES public.person(id);


--
-- Name: sampling_is_performed_by fk_65; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sampling_is_performed_by
    ADD CONSTRAINT fk_65 FOREIGN KEY (sampling_fk) REFERENCES public.sampling(id) ON DELETE CASCADE;


--
-- Name: storage_box fk_66; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.storage_box
    ADD CONSTRAINT fk_66 FOREIGN KEY (collection_type_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: storage_box fk_67; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.storage_box
    ADD CONSTRAINT fk_67 FOREIGN KEY (collection_code_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: storage_box fk_68; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.storage_box
    ADD CONSTRAINT fk_68 FOREIGN KEY (box_type_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: internal_biological_material fk_69; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_biological_material
    ADD CONSTRAINT fk_69 FOREIGN KEY (date_precision_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: external_sequence_is_published_in fk_7; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_sequence_is_published_in
    ADD CONSTRAINT fk_7 FOREIGN KEY (source_fk) REFERENCES public.source(id) ON DELETE CASCADE;


--
-- Name: internal_biological_material fk_70; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_biological_material
    ADD CONSTRAINT fk_70 FOREIGN KEY (pigmentation_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: internal_biological_material fk_71; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_biological_material
    ADD CONSTRAINT fk_71 FOREIGN KEY (eyes_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: internal_biological_material fk_72; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_biological_material
    ADD CONSTRAINT fk_72 FOREIGN KEY (sampling_fk) REFERENCES public.sampling(id);


--
-- Name: internal_biological_material fk_73; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.internal_biological_material
    ADD CONSTRAINT fk_73 FOREIGN KEY (storage_box_fk) REFERENCES public.storage_box(id);


--
-- Name: identified_species fk_74; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.identified_species
    ADD CONSTRAINT fk_74 FOREIGN KEY (identification_criterion_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: identified_species fk_75; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.identified_species
    ADD CONSTRAINT fk_75 FOREIGN KEY (date_precision_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: identified_species fk_76; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.identified_species
    ADD CONSTRAINT fk_76 FOREIGN KEY (external_sequence_fk) REFERENCES public.external_sequence(id) ON DELETE CASCADE;


--
-- Name: identified_species fk_77; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.identified_species
    ADD CONSTRAINT fk_77 FOREIGN KEY (external_biological_material_fk) REFERENCES public.external_biological_material(id) ON DELETE CASCADE;


--
-- Name: identified_species fk_78; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.identified_species
    ADD CONSTRAINT fk_78 FOREIGN KEY (internal_biological_material_fk) REFERENCES public.internal_biological_material(id) ON DELETE CASCADE;


--
-- Name: identified_species fk_79; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.identified_species
    ADD CONSTRAINT fk_79 FOREIGN KEY (taxon_fk) REFERENCES public.taxon(id);


--
-- Name: external_sequence_is_published_in fk_8; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.external_sequence_is_published_in
    ADD CONSTRAINT fk_8 FOREIGN KEY (external_sequence_fk) REFERENCES public.external_sequence(id) ON DELETE CASCADE;


--
-- Name: identified_species fk_80; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.identified_species
    ADD CONSTRAINT fk_80 FOREIGN KEY (specimen_fk) REFERENCES public.specimen(id) ON DELETE CASCADE;


--
-- Name: identified_species fk_801c3911b669f53d; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.identified_species
    ADD CONSTRAINT fk_801c3911b669f53d FOREIGN KEY (type_material_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: identified_species fk_81; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.identified_species
    ADD CONSTRAINT fk_81 FOREIGN KEY (internal_sequence_fk) REFERENCES public.internal_sequence(id) ON DELETE CASCADE;


--
-- Name: specimen fk_82; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.specimen
    ADD CONSTRAINT fk_82 FOREIGN KEY (specimen_type_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: specimen fk_83; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.specimen
    ADD CONSTRAINT fk_83 FOREIGN KEY (internal_biological_material_fk) REFERENCES public.internal_biological_material(id);


--
-- Name: dna fk_84; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dna
    ADD CONSTRAINT fk_84 FOREIGN KEY (date_precision_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: dna fk_85; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dna
    ADD CONSTRAINT fk_85 FOREIGN KEY (dna_extraction_method_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: dna fk_86; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dna
    ADD CONSTRAINT fk_86 FOREIGN KEY (specimen_fk) REFERENCES public.specimen(id);


--
-- Name: dna fk_87; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dna
    ADD CONSTRAINT fk_87 FOREIGN KEY (dna_quality_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: dna fk_88; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dna
    ADD CONSTRAINT fk_88 FOREIGN KEY (storage_box_fk) REFERENCES public.storage_box(id);


--
-- Name: pcr fk_89; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pcr
    ADD CONSTRAINT fk_89 FOREIGN KEY (gene_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: species_is_identified_by fk_9; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.species_is_identified_by
    ADD CONSTRAINT fk_9 FOREIGN KEY (identified_species_fk) REFERENCES public.identified_species(id) ON DELETE CASCADE;


--
-- Name: pcr fk_90; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pcr
    ADD CONSTRAINT fk_90 FOREIGN KEY (pcr_quality_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: pcr fk_91; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pcr
    ADD CONSTRAINT fk_91 FOREIGN KEY (pcr_specificity_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: pcr fk_92; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pcr
    ADD CONSTRAINT fk_92 FOREIGN KEY (forward_primer_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: pcr fk_93; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pcr
    ADD CONSTRAINT fk_93 FOREIGN KEY (reverse_primer_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: pcr fk_94; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pcr
    ADD CONSTRAINT fk_94 FOREIGN KEY (date_precision_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: pcr fk_95; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pcr
    ADD CONSTRAINT fk_95 FOREIGN KEY (dna_fk) REFERENCES public.dna(id);


--
-- Name: chromatogram fk_96; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.chromatogram
    ADD CONSTRAINT fk_96 FOREIGN KEY (chromato_primer_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: chromatogram fk_97; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.chromatogram
    ADD CONSTRAINT fk_97 FOREIGN KEY (chromato_quality_voc_fk) REFERENCES public.vocabulary(id);


--
-- Name: chromatogram fk_98; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.chromatogram
    ADD CONSTRAINT fk_98 FOREIGN KEY (institution_fk) REFERENCES public.institution(id);


--
-- Name: chromatogram fk_99; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.chromatogram
    ADD CONSTRAINT fk_99 FOREIGN KEY (pcr_fk) REFERENCES public.pcr(id);


--
-- PostgreSQL database dump complete
--

