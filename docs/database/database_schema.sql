--
-- PostgreSQL database dump
--

-- Dumped from database version 9.4.26
-- Dumped by pg_dump version 12.0

-- Started on 2020-07-24 14:58:15

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
-- TOC entry 3 (class 3079 OID 16394)
-- Name: cube; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS cube WITH SCHEMA public;


--
-- TOC entry 2753 (class 0 OID 0)
-- Dependencies: 3
-- Name: EXTENSION cube; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION cube IS 'data type for multidimensional cubes';


--
-- TOC entry 2 (class 3079 OID 16466)
-- Name: earthdistance; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS earthdistance WITH SCHEMA public;


--
-- TOC entry 2754 (class 0 OID 0)
-- Dependencies: 2
-- Name: EXTENSION earthdistance; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION earthdistance IS 'calculate great-circle distances on the surface of the Earth';


--
-- TOC entry 321 (class 1255 OID 16481)
-- Name: maj_datecre_datemaj_commune(); Type: FUNCTION; Schema: public; Owner: postgres
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


ALTER FUNCTION public.maj_datecre_datemaj_commune() OWNER TO postgres;

SET default_tablespace = '';

--
-- TOC entry 175 (class 1259 OID 16482)
-- Name: chromatogram; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.chromatogram OWNER TO postgres;

--
-- TOC entry 176 (class 1259 OID 16488)
-- Name: chromatogram_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.chromatogram_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.chromatogram_id_seq OWNER TO postgres;

--
-- TOC entry 2755 (class 0 OID 0)
-- Dependencies: 176
-- Name: chromatogram_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.chromatogram_id_seq OWNED BY public.chromatogram.id;


--
-- TOC entry 177 (class 1259 OID 16490)
-- Name: chromatogram_is_processed_to; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.chromatogram_is_processed_to OWNER TO postgres;

--
-- TOC entry 178 (class 1259 OID 16493)
-- Name: chromatogram_is_processed_to_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.chromatogram_is_processed_to_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.chromatogram_is_processed_to_id_seq OWNER TO postgres;

--
-- TOC entry 2756 (class 0 OID 0)
-- Dependencies: 178
-- Name: chromatogram_is_processed_to_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.chromatogram_is_processed_to_id_seq OWNED BY public.chromatogram_is_processed_to.id;


--
-- TOC entry 179 (class 1259 OID 16495)
-- Name: composition_of_internal_biological_material; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.composition_of_internal_biological_material OWNER TO postgres;

--
-- TOC entry 180 (class 1259 OID 16501)
-- Name: composition_of_internal_biological_material_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.composition_of_internal_biological_material_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.composition_of_internal_biological_material_id_seq OWNER TO postgres;

--
-- TOC entry 2757 (class 0 OID 0)
-- Dependencies: 180
-- Name: composition_of_internal_biological_material_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.composition_of_internal_biological_material_id_seq OWNED BY public.composition_of_internal_biological_material.id;


--
-- TOC entry 181 (class 1259 OID 16503)
-- Name: country; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.country OWNER TO postgres;

--
-- TOC entry 182 (class 1259 OID 16509)
-- Name: country_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.country_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.country_id_seq OWNER TO postgres;

--
-- TOC entry 2758 (class 0 OID 0)
-- Dependencies: 182
-- Name: country_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.country_id_seq OWNED BY public.country.id;


--
-- TOC entry 183 (class 1259 OID 16511)
-- Name: dna; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.dna OWNER TO postgres;

--
-- TOC entry 184 (class 1259 OID 16517)
-- Name: dna_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.dna_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dna_id_seq OWNER TO postgres;

--
-- TOC entry 2759 (class 0 OID 0)
-- Dependencies: 184
-- Name: dna_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.dna_id_seq OWNED BY public.dna.id;


--
-- TOC entry 185 (class 1259 OID 16519)
-- Name: dna_is_extracted_by; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.dna_is_extracted_by OWNER TO postgres;

--
-- TOC entry 186 (class 1259 OID 16522)
-- Name: dna_is_extracted_by_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.dna_is_extracted_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dna_is_extracted_by_id_seq OWNER TO postgres;

--
-- TOC entry 2760 (class 0 OID 0)
-- Dependencies: 186
-- Name: dna_is_extracted_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.dna_is_extracted_by_id_seq OWNED BY public.dna_is_extracted_by.id;


--
-- TOC entry 187 (class 1259 OID 16524)
-- Name: external_biological_material; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.external_biological_material OWNER TO postgres;

--
-- TOC entry 188 (class 1259 OID 16530)
-- Name: external_biological_material_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.external_biological_material_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.external_biological_material_id_seq OWNER TO postgres;

--
-- TOC entry 2761 (class 0 OID 0)
-- Dependencies: 188
-- Name: external_biological_material_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.external_biological_material_id_seq OWNED BY public.external_biological_material.id;


--
-- TOC entry 189 (class 1259 OID 16532)
-- Name: external_biological_material_is_processed_by; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.external_biological_material_is_processed_by OWNER TO postgres;

--
-- TOC entry 190 (class 1259 OID 16535)
-- Name: external_biological_material_is_processed_by_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.external_biological_material_is_processed_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.external_biological_material_is_processed_by_id_seq OWNER TO postgres;

--
-- TOC entry 2762 (class 0 OID 0)
-- Dependencies: 190
-- Name: external_biological_material_is_processed_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.external_biological_material_is_processed_by_id_seq OWNED BY public.external_biological_material_is_processed_by.id;


--
-- TOC entry 191 (class 1259 OID 16537)
-- Name: external_biological_material_is_published_in; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.external_biological_material_is_published_in OWNER TO postgres;

--
-- TOC entry 192 (class 1259 OID 16540)
-- Name: external_biological_material_is_published_in_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.external_biological_material_is_published_in_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.external_biological_material_is_published_in_id_seq OWNER TO postgres;

--
-- TOC entry 2763 (class 0 OID 0)
-- Dependencies: 192
-- Name: external_biological_material_is_published_in_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.external_biological_material_is_published_in_id_seq OWNED BY public.external_biological_material_is_published_in.id;


--
-- TOC entry 193 (class 1259 OID 16542)
-- Name: external_sequence; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.external_sequence OWNER TO postgres;

--
-- TOC entry 194 (class 1259 OID 16548)
-- Name: external_sequence_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.external_sequence_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.external_sequence_id_seq OWNER TO postgres;

--
-- TOC entry 2764 (class 0 OID 0)
-- Dependencies: 194
-- Name: external_sequence_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.external_sequence_id_seq OWNED BY public.external_sequence.id;


--
-- TOC entry 195 (class 1259 OID 16550)
-- Name: external_sequence_is_entered_by; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.external_sequence_is_entered_by OWNER TO postgres;

--
-- TOC entry 196 (class 1259 OID 16553)
-- Name: external_sequence_is_entered_by_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.external_sequence_is_entered_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.external_sequence_is_entered_by_id_seq OWNER TO postgres;

--
-- TOC entry 2765 (class 0 OID 0)
-- Dependencies: 196
-- Name: external_sequence_is_entered_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.external_sequence_is_entered_by_id_seq OWNED BY public.external_sequence_is_entered_by.id;


--
-- TOC entry 197 (class 1259 OID 16555)
-- Name: external_sequence_is_published_in; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.external_sequence_is_published_in OWNER TO postgres;

--
-- TOC entry 198 (class 1259 OID 16558)
-- Name: external_sequence_is_published_in_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.external_sequence_is_published_in_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.external_sequence_is_published_in_id_seq OWNER TO postgres;

--
-- TOC entry 2766 (class 0 OID 0)
-- Dependencies: 198
-- Name: external_sequence_is_published_in_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.external_sequence_is_published_in_id_seq OWNED BY public.external_sequence_is_published_in.id;


--
-- TOC entry 199 (class 1259 OID 16560)
-- Name: has_targeted_taxa; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.has_targeted_taxa OWNER TO postgres;

--
-- TOC entry 200 (class 1259 OID 16563)
-- Name: has_targeted_taxa_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.has_targeted_taxa_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.has_targeted_taxa_id_seq OWNER TO postgres;

--
-- TOC entry 2767 (class 0 OID 0)
-- Dependencies: 200
-- Name: has_targeted_taxa_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.has_targeted_taxa_id_seq OWNED BY public.has_targeted_taxa.id;


--
-- TOC entry 201 (class 1259 OID 16565)
-- Name: identified_species; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.identified_species OWNER TO postgres;

--
-- TOC entry 202 (class 1259 OID 16571)
-- Name: identified_species_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.identified_species_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.identified_species_id_seq OWNER TO postgres;

--
-- TOC entry 2768 (class 0 OID 0)
-- Dependencies: 202
-- Name: identified_species_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.identified_species_id_seq OWNED BY public.identified_species.id;


--
-- TOC entry 203 (class 1259 OID 16573)
-- Name: institution; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.institution OWNER TO postgres;

--
-- TOC entry 204 (class 1259 OID 16579)
-- Name: institution_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.institution_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.institution_id_seq OWNER TO postgres;

--
-- TOC entry 2769 (class 0 OID 0)
-- Dependencies: 204
-- Name: institution_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.institution_id_seq OWNED BY public.institution.id;


--
-- TOC entry 205 (class 1259 OID 16581)
-- Name: internal_biological_material; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.internal_biological_material OWNER TO postgres;

--
-- TOC entry 206 (class 1259 OID 16587)
-- Name: internal_biological_material_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.internal_biological_material_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.internal_biological_material_id_seq OWNER TO postgres;

--
-- TOC entry 2770 (class 0 OID 0)
-- Dependencies: 206
-- Name: internal_biological_material_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.internal_biological_material_id_seq OWNED BY public.internal_biological_material.id;


--
-- TOC entry 207 (class 1259 OID 16589)
-- Name: internal_biological_material_is_published_in; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.internal_biological_material_is_published_in OWNER TO postgres;

--
-- TOC entry 208 (class 1259 OID 16592)
-- Name: internal_biological_material_is_published_in_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.internal_biological_material_is_published_in_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.internal_biological_material_is_published_in_id_seq OWNER TO postgres;

--
-- TOC entry 2771 (class 0 OID 0)
-- Dependencies: 208
-- Name: internal_biological_material_is_published_in_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.internal_biological_material_is_published_in_id_seq OWNED BY public.internal_biological_material_is_published_in.id;


--
-- TOC entry 209 (class 1259 OID 16594)
-- Name: internal_biological_material_is_treated_by; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.internal_biological_material_is_treated_by OWNER TO postgres;

--
-- TOC entry 210 (class 1259 OID 16597)
-- Name: internal_biological_material_is_treated_by_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.internal_biological_material_is_treated_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.internal_biological_material_is_treated_by_id_seq OWNER TO postgres;

--
-- TOC entry 2772 (class 0 OID 0)
-- Dependencies: 210
-- Name: internal_biological_material_is_treated_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.internal_biological_material_is_treated_by_id_seq OWNED BY public.internal_biological_material_is_treated_by.id;


--
-- TOC entry 211 (class 1259 OID 16599)
-- Name: internal_sequence; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.internal_sequence OWNER TO postgres;

--
-- TOC entry 212 (class 1259 OID 16605)
-- Name: internal_sequence_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.internal_sequence_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.internal_sequence_id_seq OWNER TO postgres;

--
-- TOC entry 2773 (class 0 OID 0)
-- Dependencies: 212
-- Name: internal_sequence_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.internal_sequence_id_seq OWNED BY public.internal_sequence.id;


--
-- TOC entry 213 (class 1259 OID 16607)
-- Name: internal_sequence_is_assembled_by; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.internal_sequence_is_assembled_by OWNER TO postgres;

--
-- TOC entry 214 (class 1259 OID 16610)
-- Name: internal_sequence_is_assembled_by_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.internal_sequence_is_assembled_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.internal_sequence_is_assembled_by_id_seq OWNER TO postgres;

--
-- TOC entry 2774 (class 0 OID 0)
-- Dependencies: 214
-- Name: internal_sequence_is_assembled_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.internal_sequence_is_assembled_by_id_seq OWNED BY public.internal_sequence_is_assembled_by.id;


--
-- TOC entry 215 (class 1259 OID 16612)
-- Name: internal_sequence_is_published_in; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.internal_sequence_is_published_in OWNER TO postgres;

--
-- TOC entry 216 (class 1259 OID 16615)
-- Name: internal_sequence_is_published_in_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.internal_sequence_is_published_in_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.internal_sequence_is_published_in_id_seq OWNER TO postgres;

--
-- TOC entry 2775 (class 0 OID 0)
-- Dependencies: 216
-- Name: internal_sequence_is_published_in_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.internal_sequence_is_published_in_id_seq OWNED BY public.internal_sequence_is_published_in.id;


--
-- TOC entry 217 (class 1259 OID 16617)
-- Name: motu; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.motu OWNER TO postgres;

--
-- TOC entry 218 (class 1259 OID 16623)
-- Name: motu_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.motu_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.motu_id_seq OWNER TO postgres;

--
-- TOC entry 2776 (class 0 OID 0)
-- Dependencies: 218
-- Name: motu_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.motu_id_seq OWNED BY public.motu.id;


--
-- TOC entry 219 (class 1259 OID 16625)
-- Name: motu_is_generated_by; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.motu_is_generated_by OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 16628)
-- Name: motu_is_generated_by_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.motu_is_generated_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.motu_is_generated_by_id_seq OWNER TO postgres;

--
-- TOC entry 2777 (class 0 OID 0)
-- Dependencies: 220
-- Name: motu_is_generated_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.motu_is_generated_by_id_seq OWNED BY public.motu_is_generated_by.id;


--
-- TOC entry 221 (class 1259 OID 16630)
-- Name: motu_number; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.motu_number OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 16633)
-- Name: motu_number_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.motu_number_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.motu_number_id_seq OWNER TO postgres;

--
-- TOC entry 2778 (class 0 OID 0)
-- Dependencies: 222
-- Name: motu_number_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.motu_number_id_seq OWNED BY public.motu_number.id;


--
-- TOC entry 223 (class 1259 OID 16635)
-- Name: municipality; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.municipality OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 16641)
-- Name: municipality_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.municipality_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.municipality_id_seq OWNER TO postgres;

--
-- TOC entry 2779 (class 0 OID 0)
-- Dependencies: 224
-- Name: municipality_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.municipality_id_seq OWNED BY public.municipality.id;


--
-- TOC entry 225 (class 1259 OID 16643)
-- Name: pcr; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.pcr OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 16649)
-- Name: pcr_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pcr_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.pcr_id_seq OWNER TO postgres;

--
-- TOC entry 2780 (class 0 OID 0)
-- Dependencies: 226
-- Name: pcr_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pcr_id_seq OWNED BY public.pcr.id;


--
-- TOC entry 227 (class 1259 OID 16651)
-- Name: pcr_is_done_by; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.pcr_is_done_by OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 16654)
-- Name: pcr_is_done_by_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pcr_is_done_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.pcr_is_done_by_id_seq OWNER TO postgres;

--
-- TOC entry 2781 (class 0 OID 0)
-- Dependencies: 228
-- Name: pcr_is_done_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pcr_is_done_by_id_seq OWNED BY public.pcr_is_done_by.id;


--
-- TOC entry 229 (class 1259 OID 16656)
-- Name: person; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.person OWNER TO postgres;

--
-- TOC entry 230 (class 1259 OID 16662)
-- Name: person_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.person_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.person_id_seq OWNER TO postgres;

--
-- TOC entry 2782 (class 0 OID 0)
-- Dependencies: 230
-- Name: person_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.person_id_seq OWNED BY public.person.id;


--
-- TOC entry 231 (class 1259 OID 16664)
-- Name: program; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.program OWNER TO postgres;

--
-- TOC entry 232 (class 1259 OID 16670)
-- Name: program_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.program_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.program_id_seq OWNER TO postgres;

--
-- TOC entry 2783 (class 0 OID 0)
-- Dependencies: 232
-- Name: program_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.program_id_seq OWNED BY public.program.id;


--
-- TOC entry 233 (class 1259 OID 16672)
-- Name: sample_is_fixed_with; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.sample_is_fixed_with OWNER TO postgres;

--
-- TOC entry 234 (class 1259 OID 16675)
-- Name: sample_is_fixed_with_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sample_is_fixed_with_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sample_is_fixed_with_id_seq OWNER TO postgres;

--
-- TOC entry 2784 (class 0 OID 0)
-- Dependencies: 234
-- Name: sample_is_fixed_with_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sample_is_fixed_with_id_seq OWNED BY public.sample_is_fixed_with.id;


--
-- TOC entry 235 (class 1259 OID 16677)
-- Name: sampling; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.sampling OWNER TO postgres;

--
-- TOC entry 236 (class 1259 OID 16683)
-- Name: sampling_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sampling_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sampling_id_seq OWNER TO postgres;

--
-- TOC entry 2785 (class 0 OID 0)
-- Dependencies: 236
-- Name: sampling_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sampling_id_seq OWNED BY public.sampling.id;


--
-- TOC entry 237 (class 1259 OID 16685)
-- Name: sampling_is_done_with_method; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.sampling_is_done_with_method OWNER TO postgres;

--
-- TOC entry 238 (class 1259 OID 16688)
-- Name: sampling_is_done_with_method_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sampling_is_done_with_method_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sampling_is_done_with_method_id_seq OWNER TO postgres;

--
-- TOC entry 2786 (class 0 OID 0)
-- Dependencies: 238
-- Name: sampling_is_done_with_method_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sampling_is_done_with_method_id_seq OWNED BY public.sampling_is_done_with_method.id;


--
-- TOC entry 239 (class 1259 OID 16690)
-- Name: sampling_is_funded_by; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.sampling_is_funded_by OWNER TO postgres;

--
-- TOC entry 240 (class 1259 OID 16693)
-- Name: sampling_is_funded_by_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sampling_is_funded_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sampling_is_funded_by_id_seq OWNER TO postgres;

--
-- TOC entry 2787 (class 0 OID 0)
-- Dependencies: 240
-- Name: sampling_is_funded_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sampling_is_funded_by_id_seq OWNED BY public.sampling_is_funded_by.id;


--
-- TOC entry 241 (class 1259 OID 16695)
-- Name: sampling_is_performed_by; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.sampling_is_performed_by OWNER TO postgres;

--
-- TOC entry 242 (class 1259 OID 16698)
-- Name: sampling_is_performed_by_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sampling_is_performed_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sampling_is_performed_by_id_seq OWNER TO postgres;

--
-- TOC entry 2788 (class 0 OID 0)
-- Dependencies: 242
-- Name: sampling_is_performed_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sampling_is_performed_by_id_seq OWNED BY public.sampling_is_performed_by.id;


--
-- TOC entry 243 (class 1259 OID 16700)
-- Name: site; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.site OWNER TO postgres;

--
-- TOC entry 244 (class 1259 OID 16706)
-- Name: site_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.site_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.site_id_seq OWNER TO postgres;

--
-- TOC entry 2789 (class 0 OID 0)
-- Dependencies: 244
-- Name: site_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.site_id_seq OWNED BY public.site.id;


--
-- TOC entry 245 (class 1259 OID 16708)
-- Name: slide_is_mounted_by; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.slide_is_mounted_by OWNER TO postgres;

--
-- TOC entry 246 (class 1259 OID 16711)
-- Name: slide_is_mounted_by_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.slide_is_mounted_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.slide_is_mounted_by_id_seq OWNER TO postgres;

--
-- TOC entry 2790 (class 0 OID 0)
-- Dependencies: 246
-- Name: slide_is_mounted_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.slide_is_mounted_by_id_seq OWNED BY public.slide_is_mounted_by.id;


--
-- TOC entry 247 (class 1259 OID 16713)
-- Name: source; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.source OWNER TO postgres;

--
-- TOC entry 248 (class 1259 OID 16719)
-- Name: source_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.source_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.source_id_seq OWNER TO postgres;

--
-- TOC entry 2791 (class 0 OID 0)
-- Dependencies: 248
-- Name: source_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.source_id_seq OWNED BY public.source.id;


--
-- TOC entry 249 (class 1259 OID 16721)
-- Name: source_is_entered_by; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.source_is_entered_by OWNER TO postgres;

--
-- TOC entry 250 (class 1259 OID 16724)
-- Name: source_is_entered_by_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.source_is_entered_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.source_is_entered_by_id_seq OWNER TO postgres;

--
-- TOC entry 2792 (class 0 OID 0)
-- Dependencies: 250
-- Name: source_is_entered_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.source_is_entered_by_id_seq OWNED BY public.source_is_entered_by.id;


--
-- TOC entry 251 (class 1259 OID 16726)
-- Name: species_is_identified_by; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.species_is_identified_by OWNER TO postgres;

--
-- TOC entry 252 (class 1259 OID 16729)
-- Name: species_is_identified_by_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.species_is_identified_by_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.species_is_identified_by_id_seq OWNER TO postgres;

--
-- TOC entry 2793 (class 0 OID 0)
-- Dependencies: 252
-- Name: species_is_identified_by_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.species_is_identified_by_id_seq OWNED BY public.species_is_identified_by.id;


--
-- TOC entry 253 (class 1259 OID 16731)
-- Name: specimen; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.specimen OWNER TO postgres;

--
-- TOC entry 254 (class 1259 OID 16737)
-- Name: specimen_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.specimen_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.specimen_id_seq OWNER TO postgres;

--
-- TOC entry 2794 (class 0 OID 0)
-- Dependencies: 254
-- Name: specimen_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.specimen_id_seq OWNED BY public.specimen.id;


--
-- TOC entry 255 (class 1259 OID 16739)
-- Name: specimen_slide; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.specimen_slide OWNER TO postgres;

--
-- TOC entry 256 (class 1259 OID 16745)
-- Name: specimen_slide_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.specimen_slide_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.specimen_slide_id_seq OWNER TO postgres;

--
-- TOC entry 2795 (class 0 OID 0)
-- Dependencies: 256
-- Name: specimen_slide_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.specimen_slide_id_seq OWNED BY public.specimen_slide.id;


--
-- TOC entry 257 (class 1259 OID 16747)
-- Name: storage_box; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.storage_box OWNER TO postgres;

--
-- TOC entry 258 (class 1259 OID 16753)
-- Name: storage_box_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.storage_box_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.storage_box_id_seq OWNER TO postgres;

--
-- TOC entry 2796 (class 0 OID 0)
-- Dependencies: 258
-- Name: storage_box_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.storage_box_id_seq OWNED BY public.storage_box.id;


--
-- TOC entry 259 (class 1259 OID 16755)
-- Name: taxon; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.taxon OWNER TO postgres;

--
-- TOC entry 260 (class 1259 OID 16761)
-- Name: taxon_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.taxon_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.taxon_id_seq OWNER TO postgres;

--
-- TOC entry 2797 (class 0 OID 0)
-- Dependencies: 260
-- Name: taxon_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.taxon_id_seq OWNED BY public.taxon.id;


--
-- TOC entry 261 (class 1259 OID 16763)
-- Name: user_db; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.user_db OWNER TO postgres;

--
-- TOC entry 262 (class 1259 OID 16774)
-- Name: user_db_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.user_db_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user_db_id_seq OWNER TO postgres;

--
-- TOC entry 2798 (class 0 OID 0)
-- Dependencies: 262
-- Name: user_db_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.user_db_id_seq OWNED BY public.user_db.id;


--
-- TOC entry 263 (class 1259 OID 16776)
-- Name: vocabulary; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.vocabulary OWNER TO postgres;

--
-- TOC entry 264 (class 1259 OID 16782)
-- Name: vocabulary_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.vocabulary_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.vocabulary_id_seq OWNER TO postgres;

--
-- TOC entry 2799 (class 0 OID 0)
-- Dependencies: 264
-- Name: vocabulary_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.vocabulary_id_seq OWNED BY public.vocabulary.id;


--
-- TOC entry 2236 (class 2604 OID 16784)
-- Name: chromatogram id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.chromatogram ALTER COLUMN id SET DEFAULT nextval('public.chromatogram_id_seq'::regclass);


--
-- TOC entry 2237 (class 2604 OID 16785)
-- Name: chromatogram_is_processed_to id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.chromatogram_is_processed_to ALTER COLUMN id SET DEFAULT nextval('public.chromatogram_is_processed_to_id_seq'::regclass);


--
-- TOC entry 2238 (class 2604 OID 16786)
-- Name: composition_of_internal_biological_material id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.composition_of_internal_biological_material ALTER COLUMN id SET DEFAULT nextval('public.composition_of_internal_biological_material_id_seq'::regclass);


--
-- TOC entry 2239 (class 2604 OID 16787)
-- Name: country id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.country ALTER COLUMN id SET DEFAULT nextval('public.country_id_seq'::regclass);


--
-- TOC entry 2240 (class 2604 OID 16788)
-- Name: dna id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dna ALTER COLUMN id SET DEFAULT nextval('public.dna_id_seq'::regclass);


--
-- TOC entry 2241 (class 2604 OID 16789)
-- Name: dna_is_extracted_by id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dna_is_extracted_by ALTER COLUMN id SET DEFAULT nextval('public.dna_is_extracted_by_id_seq'::regclass);


--
-- TOC entry 2242 (class 2604 OID 16790)
-- Name: external_biological_material id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_biological_material ALTER COLUMN id SET DEFAULT nextval('public.external_biological_material_id_seq'::regclass);


--
-- TOC entry 2243 (class 2604 OID 16791)
-- Name: external_biological_material_is_processed_by id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_biological_material_is_processed_by ALTER COLUMN id SET DEFAULT nextval('public.external_biological_material_is_processed_by_id_seq'::regclass);


--
-- TOC entry 2244 (class 2604 OID 16792)
-- Name: external_biological_material_is_published_in id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_biological_material_is_published_in ALTER COLUMN id SET DEFAULT nextval('public.external_biological_material_is_published_in_id_seq'::regclass);


--
-- TOC entry 2245 (class 2604 OID 16793)
-- Name: external_sequence id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_sequence ALTER COLUMN id SET DEFAULT nextval('public.external_sequence_id_seq'::regclass);


--
-- TOC entry 2246 (class 2604 OID 16794)
-- Name: external_sequence_is_entered_by id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_sequence_is_entered_by ALTER COLUMN id SET DEFAULT nextval('public.external_sequence_is_entered_by_id_seq'::regclass);


--
-- TOC entry 2247 (class 2604 OID 16795)
-- Name: external_sequence_is_published_in id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_sequence_is_published_in ALTER COLUMN id SET DEFAULT nextval('public.external_sequence_is_published_in_id_seq'::regclass);


--
-- TOC entry 2248 (class 2604 OID 16796)
-- Name: has_targeted_taxa id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.has_targeted_taxa ALTER COLUMN id SET DEFAULT nextval('public.has_targeted_taxa_id_seq'::regclass);


--
-- TOC entry 2249 (class 2604 OID 16797)
-- Name: identified_species id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.identified_species ALTER COLUMN id SET DEFAULT nextval('public.identified_species_id_seq'::regclass);


--
-- TOC entry 2250 (class 2604 OID 16798)
-- Name: institution id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.institution ALTER COLUMN id SET DEFAULT nextval('public.institution_id_seq'::regclass);


--
-- TOC entry 2251 (class 2604 OID 16799)
-- Name: internal_biological_material id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_biological_material ALTER COLUMN id SET DEFAULT nextval('public.internal_biological_material_id_seq'::regclass);


--
-- TOC entry 2252 (class 2604 OID 16800)
-- Name: internal_biological_material_is_published_in id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_biological_material_is_published_in ALTER COLUMN id SET DEFAULT nextval('public.internal_biological_material_is_published_in_id_seq'::regclass);


--
-- TOC entry 2253 (class 2604 OID 16801)
-- Name: internal_biological_material_is_treated_by id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_biological_material_is_treated_by ALTER COLUMN id SET DEFAULT nextval('public.internal_biological_material_is_treated_by_id_seq'::regclass);


--
-- TOC entry 2254 (class 2604 OID 16802)
-- Name: internal_sequence id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_sequence ALTER COLUMN id SET DEFAULT nextval('public.internal_sequence_id_seq'::regclass);


--
-- TOC entry 2255 (class 2604 OID 16803)
-- Name: internal_sequence_is_assembled_by id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_sequence_is_assembled_by ALTER COLUMN id SET DEFAULT nextval('public.internal_sequence_is_assembled_by_id_seq'::regclass);


--
-- TOC entry 2256 (class 2604 OID 16804)
-- Name: internal_sequence_is_published_in id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_sequence_is_published_in ALTER COLUMN id SET DEFAULT nextval('public.internal_sequence_is_published_in_id_seq'::regclass);


--
-- TOC entry 2257 (class 2604 OID 16805)
-- Name: motu id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.motu ALTER COLUMN id SET DEFAULT nextval('public.motu_id_seq'::regclass);


--
-- TOC entry 2258 (class 2604 OID 16806)
-- Name: motu_is_generated_by id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.motu_is_generated_by ALTER COLUMN id SET DEFAULT nextval('public.motu_is_generated_by_id_seq'::regclass);


--
-- TOC entry 2259 (class 2604 OID 16807)
-- Name: motu_number id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.motu_number ALTER COLUMN id SET DEFAULT nextval('public.motu_number_id_seq'::regclass);


--
-- TOC entry 2260 (class 2604 OID 16808)
-- Name: municipality id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.municipality ALTER COLUMN id SET DEFAULT nextval('public.municipality_id_seq'::regclass);


--
-- TOC entry 2261 (class 2604 OID 16809)
-- Name: pcr id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pcr ALTER COLUMN id SET DEFAULT nextval('public.pcr_id_seq'::regclass);


--
-- TOC entry 2262 (class 2604 OID 16810)
-- Name: pcr_is_done_by id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pcr_is_done_by ALTER COLUMN id SET DEFAULT nextval('public.pcr_is_done_by_id_seq'::regclass);


--
-- TOC entry 2263 (class 2604 OID 16811)
-- Name: person id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.person ALTER COLUMN id SET DEFAULT nextval('public.person_id_seq'::regclass);


--
-- TOC entry 2264 (class 2604 OID 16812)
-- Name: program id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.program ALTER COLUMN id SET DEFAULT nextval('public.program_id_seq'::regclass);


--
-- TOC entry 2265 (class 2604 OID 16813)
-- Name: sample_is_fixed_with id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sample_is_fixed_with ALTER COLUMN id SET DEFAULT nextval('public.sample_is_fixed_with_id_seq'::regclass);


--
-- TOC entry 2266 (class 2604 OID 16814)
-- Name: sampling id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sampling ALTER COLUMN id SET DEFAULT nextval('public.sampling_id_seq'::regclass);


--
-- TOC entry 2267 (class 2604 OID 16815)
-- Name: sampling_is_done_with_method id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sampling_is_done_with_method ALTER COLUMN id SET DEFAULT nextval('public.sampling_is_done_with_method_id_seq'::regclass);


--
-- TOC entry 2268 (class 2604 OID 16816)
-- Name: sampling_is_funded_by id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sampling_is_funded_by ALTER COLUMN id SET DEFAULT nextval('public.sampling_is_funded_by_id_seq'::regclass);


--
-- TOC entry 2269 (class 2604 OID 16817)
-- Name: sampling_is_performed_by id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sampling_is_performed_by ALTER COLUMN id SET DEFAULT nextval('public.sampling_is_performed_by_id_seq'::regclass);


--
-- TOC entry 2270 (class 2604 OID 16818)
-- Name: site id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.site ALTER COLUMN id SET DEFAULT nextval('public.site_id_seq'::regclass);


--
-- TOC entry 2271 (class 2604 OID 16819)
-- Name: slide_is_mounted_by id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.slide_is_mounted_by ALTER COLUMN id SET DEFAULT nextval('public.slide_is_mounted_by_id_seq'::regclass);


--
-- TOC entry 2272 (class 2604 OID 16820)
-- Name: source id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.source ALTER COLUMN id SET DEFAULT nextval('public.source_id_seq'::regclass);


--
-- TOC entry 2273 (class 2604 OID 16821)
-- Name: source_is_entered_by id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.source_is_entered_by ALTER COLUMN id SET DEFAULT nextval('public.source_is_entered_by_id_seq'::regclass);


--
-- TOC entry 2274 (class 2604 OID 16822)
-- Name: species_is_identified_by id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.species_is_identified_by ALTER COLUMN id SET DEFAULT nextval('public.species_is_identified_by_id_seq'::regclass);


--
-- TOC entry 2275 (class 2604 OID 16823)
-- Name: specimen id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.specimen ALTER COLUMN id SET DEFAULT nextval('public.specimen_id_seq'::regclass);


--
-- TOC entry 2276 (class 2604 OID 16824)
-- Name: specimen_slide id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.specimen_slide ALTER COLUMN id SET DEFAULT nextval('public.specimen_slide_id_seq'::regclass);


--
-- TOC entry 2277 (class 2604 OID 16825)
-- Name: storage_box id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.storage_box ALTER COLUMN id SET DEFAULT nextval('public.storage_box_id_seq'::regclass);


--
-- TOC entry 2278 (class 2604 OID 16826)
-- Name: taxon id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.taxon ALTER COLUMN id SET DEFAULT nextval('public.taxon_id_seq'::regclass);


--
-- TOC entry 2284 (class 2604 OID 16827)
-- Name: user_db id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_db ALTER COLUMN id SET DEFAULT nextval('public.user_db_id_seq'::regclass);


--
-- TOC entry 2285 (class 2604 OID 16828)
-- Name: vocabulary id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vocabulary ALTER COLUMN id SET DEFAULT nextval('public.vocabulary_id_seq'::regclass);


--
-- TOC entry 2291 (class 2606 OID 16830)
-- Name: chromatogram pk_chromatogram; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.chromatogram
    ADD CONSTRAINT pk_chromatogram PRIMARY KEY (id);


--
-- TOC entry 2297 (class 2606 OID 16832)
-- Name: chromatogram_is_processed_to pk_chromatogram_is_processed_to; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.chromatogram_is_processed_to
    ADD CONSTRAINT pk_chromatogram_is_processed_to PRIMARY KEY (id);


--
-- TOC entry 2301 (class 2606 OID 16834)
-- Name: composition_of_internal_biological_material pk_composition_of_internal_biological_material; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.composition_of_internal_biological_material
    ADD CONSTRAINT pk_composition_of_internal_biological_material PRIMARY KEY (id);


--
-- TOC entry 2303 (class 2606 OID 16836)
-- Name: country pk_country; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.country
    ADD CONSTRAINT pk_country PRIMARY KEY (id);


--
-- TOC entry 2312 (class 2606 OID 16838)
-- Name: dna pk_dna; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dna
    ADD CONSTRAINT pk_dna PRIMARY KEY (id);


--
-- TOC entry 2318 (class 2606 OID 16840)
-- Name: dna_is_extracted_by pk_dna_is_extracted_by; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dna_is_extracted_by
    ADD CONSTRAINT pk_dna_is_extracted_by PRIMARY KEY (id);


--
-- TOC entry 2325 (class 2606 OID 16842)
-- Name: external_biological_material pk_external_biological_material; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_biological_material
    ADD CONSTRAINT pk_external_biological_material PRIMARY KEY (id);


--
-- TOC entry 2331 (class 2606 OID 16844)
-- Name: external_biological_material_is_processed_by pk_external_biological_material_is_processed_by; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_biological_material_is_processed_by
    ADD CONSTRAINT pk_external_biological_material_is_processed_by PRIMARY KEY (id);


--
-- TOC entry 2335 (class 2606 OID 16846)
-- Name: external_biological_material_is_published_in pk_external_biological_material_is_published_in; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_biological_material_is_published_in
    ADD CONSTRAINT pk_external_biological_material_is_published_in PRIMARY KEY (id);


--
-- TOC entry 2342 (class 2606 OID 16848)
-- Name: external_sequence pk_external_sequence; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_sequence
    ADD CONSTRAINT pk_external_sequence PRIMARY KEY (id);


--
-- TOC entry 2350 (class 2606 OID 16850)
-- Name: external_sequence_is_entered_by pk_external_sequence_is_entered_by; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_sequence_is_entered_by
    ADD CONSTRAINT pk_external_sequence_is_entered_by PRIMARY KEY (id);


--
-- TOC entry 2354 (class 2606 OID 16852)
-- Name: external_sequence_is_published_in pk_external_sequence_is_published_in; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_sequence_is_published_in
    ADD CONSTRAINT pk_external_sequence_is_published_in PRIMARY KEY (id);


--
-- TOC entry 2358 (class 2606 OID 16854)
-- Name: has_targeted_taxa pk_has_targeted_taxa; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.has_targeted_taxa
    ADD CONSTRAINT pk_has_targeted_taxa PRIMARY KEY (id);


--
-- TOC entry 2369 (class 2606 OID 16856)
-- Name: identified_species pk_identified_species; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.identified_species
    ADD CONSTRAINT pk_identified_species PRIMARY KEY (id);


--
-- TOC entry 2371 (class 2606 OID 16858)
-- Name: institution pk_institution; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.institution
    ADD CONSTRAINT pk_institution PRIMARY KEY (id);


--
-- TOC entry 2380 (class 2606 OID 16860)
-- Name: internal_biological_material pk_internal_biological_material; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_biological_material
    ADD CONSTRAINT pk_internal_biological_material PRIMARY KEY (id);


--
-- TOC entry 2386 (class 2606 OID 16862)
-- Name: internal_biological_material_is_published_in pk_internal_biological_material_is_published_in; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_biological_material_is_published_in
    ADD CONSTRAINT pk_internal_biological_material_is_published_in PRIMARY KEY (id);


--
-- TOC entry 2390 (class 2606 OID 16864)
-- Name: internal_biological_material_is_treated_by pk_internal_biological_material_is_treated_by; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_biological_material_is_treated_by
    ADD CONSTRAINT pk_internal_biological_material_is_treated_by PRIMARY KEY (id);


--
-- TOC entry 2394 (class 2606 OID 16866)
-- Name: internal_sequence pk_internal_sequence; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_sequence
    ADD CONSTRAINT pk_internal_sequence PRIMARY KEY (id);


--
-- TOC entry 2402 (class 2606 OID 16868)
-- Name: internal_sequence_is_assembled_by pk_internal_sequence_is_assembled_by; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_sequence_is_assembled_by
    ADD CONSTRAINT pk_internal_sequence_is_assembled_by PRIMARY KEY (id);


--
-- TOC entry 2406 (class 2606 OID 16870)
-- Name: internal_sequence_is_published_in pk_internal_sequence_is_published_in; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_sequence_is_published_in
    ADD CONSTRAINT pk_internal_sequence_is_published_in PRIMARY KEY (id);


--
-- TOC entry 2408 (class 2606 OID 16872)
-- Name: motu pk_motu; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.motu
    ADD CONSTRAINT pk_motu PRIMARY KEY (id);


--
-- TOC entry 2412 (class 2606 OID 16874)
-- Name: motu_is_generated_by pk_motu_is_generated_by; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.motu_is_generated_by
    ADD CONSTRAINT pk_motu_is_generated_by PRIMARY KEY (id);


--
-- TOC entry 2418 (class 2606 OID 16876)
-- Name: motu_number pk_motu_number; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.motu_number
    ADD CONSTRAINT pk_motu_number PRIMARY KEY (id);


--
-- TOC entry 2421 (class 2606 OID 16878)
-- Name: municipality pk_municipality; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.municipality
    ADD CONSTRAINT pk_municipality PRIMARY KEY (id);


--
-- TOC entry 2432 (class 2606 OID 16880)
-- Name: pcr pk_pcr; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pcr
    ADD CONSTRAINT pk_pcr PRIMARY KEY (id);


--
-- TOC entry 2438 (class 2606 OID 16882)
-- Name: pcr_is_done_by pk_pcr_is_done_by; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pcr_is_done_by
    ADD CONSTRAINT pk_pcr_is_done_by PRIMARY KEY (id);


--
-- TOC entry 2441 (class 2606 OID 16884)
-- Name: person pk_person; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.person
    ADD CONSTRAINT pk_person PRIMARY KEY (id);


--
-- TOC entry 2445 (class 2606 OID 16886)
-- Name: program pk_program; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.program
    ADD CONSTRAINT pk_program PRIMARY KEY (id);


--
-- TOC entry 2451 (class 2606 OID 16888)
-- Name: sample_is_fixed_with pk_sample_is_fixed_with; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sample_is_fixed_with
    ADD CONSTRAINT pk_sample_is_fixed_with PRIMARY KEY (id);


--
-- TOC entry 2456 (class 2606 OID 16890)
-- Name: sampling pk_sampling; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sampling
    ADD CONSTRAINT pk_sampling PRIMARY KEY (id);


--
-- TOC entry 2462 (class 2606 OID 16892)
-- Name: sampling_is_done_with_method pk_sampling_is_done_with_method; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sampling_is_done_with_method
    ADD CONSTRAINT pk_sampling_is_done_with_method PRIMARY KEY (id);


--
-- TOC entry 2466 (class 2606 OID 16894)
-- Name: sampling_is_funded_by pk_sampling_is_funded_by; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sampling_is_funded_by
    ADD CONSTRAINT pk_sampling_is_funded_by PRIMARY KEY (id);


--
-- TOC entry 2470 (class 2606 OID 16896)
-- Name: sampling_is_performed_by pk_sampling_is_performed_by; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sampling_is_performed_by
    ADD CONSTRAINT pk_sampling_is_performed_by PRIMARY KEY (id);


--
-- TOC entry 2477 (class 2606 OID 16898)
-- Name: site pk_site; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.site
    ADD CONSTRAINT pk_site PRIMARY KEY (id);


--
-- TOC entry 2483 (class 2606 OID 16900)
-- Name: slide_is_mounted_by pk_slide_is_mounted_by; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.slide_is_mounted_by
    ADD CONSTRAINT pk_slide_is_mounted_by PRIMARY KEY (id);


--
-- TOC entry 2485 (class 2606 OID 16902)
-- Name: source pk_source; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.source
    ADD CONSTRAINT pk_source PRIMARY KEY (id);


--
-- TOC entry 2491 (class 2606 OID 16904)
-- Name: source_is_entered_by pk_source_is_entered_by; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.source_is_entered_by
    ADD CONSTRAINT pk_source_is_entered_by PRIMARY KEY (id);


--
-- TOC entry 2495 (class 2606 OID 16906)
-- Name: species_is_identified_by pk_species_is_identified_by; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.species_is_identified_by
    ADD CONSTRAINT pk_species_is_identified_by PRIMARY KEY (id);


--
-- TOC entry 2499 (class 2606 OID 16908)
-- Name: specimen pk_specimen; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.specimen
    ADD CONSTRAINT pk_specimen PRIMARY KEY (id);


--
-- TOC entry 2508 (class 2606 OID 16910)
-- Name: specimen_slide pk_specimen_slide; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.specimen_slide
    ADD CONSTRAINT pk_specimen_slide PRIMARY KEY (id);


--
-- TOC entry 2515 (class 2606 OID 16912)
-- Name: storage_box pk_storage_box; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.storage_box
    ADD CONSTRAINT pk_storage_box PRIMARY KEY (id);


--
-- TOC entry 2519 (class 2606 OID 16914)
-- Name: taxon pk_taxon; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.taxon
    ADD CONSTRAINT pk_taxon PRIMARY KEY (id);


--
-- TOC entry 2525 (class 2606 OID 16916)
-- Name: user_db pk_user_db; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_db
    ADD CONSTRAINT pk_user_db PRIMARY KEY (id);


--
-- TOC entry 2529 (class 2606 OID 16918)
-- Name: vocabulary pk_vocabulary; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vocabulary
    ADD CONSTRAINT pk_vocabulary PRIMARY KEY (id);


--
-- TOC entry 2293 (class 2606 OID 16920)
-- Name: chromatogram uk_chromatogram__chromatogram_code; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.chromatogram
    ADD CONSTRAINT uk_chromatogram__chromatogram_code UNIQUE (chromatogram_code);


--
-- TOC entry 2305 (class 2606 OID 16922)
-- Name: country uk_country__country_code; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.country
    ADD CONSTRAINT uk_country__country_code UNIQUE (country_code);


--
-- TOC entry 2314 (class 2606 OID 16924)
-- Name: dna uk_dna__dna_code; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dna
    ADD CONSTRAINT uk_dna__dna_code UNIQUE (dna_code);


--
-- TOC entry 2327 (class 2606 OID 16926)
-- Name: external_biological_material uk_external_biological_material__external_biological_material_c; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_biological_material
    ADD CONSTRAINT uk_external_biological_material__external_biological_material_c UNIQUE (external_biological_material_code);


--
-- TOC entry 2344 (class 2606 OID 16928)
-- Name: external_sequence uk_external_sequence__external_sequence_alignment_code; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_sequence
    ADD CONSTRAINT uk_external_sequence__external_sequence_alignment_code UNIQUE (external_sequence_alignment_code);


--
-- TOC entry 2346 (class 2606 OID 16930)
-- Name: external_sequence uk_external_sequence__external_sequence_code; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_sequence
    ADD CONSTRAINT uk_external_sequence__external_sequence_code UNIQUE (external_sequence_code);


--
-- TOC entry 2373 (class 2606 OID 16932)
-- Name: institution uk_institution__institution_name; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.institution
    ADD CONSTRAINT uk_institution__institution_name UNIQUE (institution_name);


--
-- TOC entry 2382 (class 2606 OID 16934)
-- Name: internal_biological_material uk_internal_biological_material__internal_biological_material_c; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_biological_material
    ADD CONSTRAINT uk_internal_biological_material__internal_biological_material_c UNIQUE (internal_biological_material_code);


--
-- TOC entry 2396 (class 2606 OID 16936)
-- Name: internal_sequence uk_internal_sequence__internal_sequence_alignment_code; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_sequence
    ADD CONSTRAINT uk_internal_sequence__internal_sequence_alignment_code UNIQUE (internal_sequence_alignment_code);


--
-- TOC entry 2398 (class 2606 OID 16938)
-- Name: internal_sequence uk_internal_sequence__internal_sequence_code; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_sequence
    ADD CONSTRAINT uk_internal_sequence__internal_sequence_code UNIQUE (internal_sequence_code);


--
-- TOC entry 2423 (class 2606 OID 16940)
-- Name: municipality uk_municipality__municipality_code; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.municipality
    ADD CONSTRAINT uk_municipality__municipality_code UNIQUE (municipality_code);


--
-- TOC entry 2434 (class 2606 OID 16942)
-- Name: pcr uk_pcr__pcr_code; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pcr
    ADD CONSTRAINT uk_pcr__pcr_code UNIQUE (pcr_code);


--
-- TOC entry 2443 (class 2606 OID 16944)
-- Name: person uk_person__person_name; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.person
    ADD CONSTRAINT uk_person__person_name UNIQUE (person_name);


--
-- TOC entry 2447 (class 2606 OID 16946)
-- Name: program uk_program__program_code; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.program
    ADD CONSTRAINT uk_program__program_code UNIQUE (program_code);


--
-- TOC entry 2458 (class 2606 OID 16948)
-- Name: sampling uk_sampling__sample_code; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sampling
    ADD CONSTRAINT uk_sampling__sample_code UNIQUE (sample_code);


--
-- TOC entry 2479 (class 2606 OID 16950)
-- Name: site uk_site__site_code; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.site
    ADD CONSTRAINT uk_site__site_code UNIQUE (site_code);


--
-- TOC entry 2487 (class 2606 OID 16952)
-- Name: source uk_source__source_code; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.source
    ADD CONSTRAINT uk_source__source_code UNIQUE (source_code);


--
-- TOC entry 2501 (class 2606 OID 16954)
-- Name: specimen uk_specimen__specimen_molecular_code; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.specimen
    ADD CONSTRAINT uk_specimen__specimen_molecular_code UNIQUE (specimen_molecular_code);


--
-- TOC entry 2503 (class 2606 OID 16956)
-- Name: specimen uk_specimen__specimen_morphological_code; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.specimen
    ADD CONSTRAINT uk_specimen__specimen_morphological_code UNIQUE (specimen_morphological_code);


--
-- TOC entry 2510 (class 2606 OID 16958)
-- Name: specimen_slide uk_specimen_slide__collection_slide_code; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.specimen_slide
    ADD CONSTRAINT uk_specimen_slide__collection_slide_code UNIQUE (collection_slide_code);


--
-- TOC entry 2517 (class 2606 OID 16960)
-- Name: storage_box uk_storage_box__box_code; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.storage_box
    ADD CONSTRAINT uk_storage_box__box_code UNIQUE (box_code);


--
-- TOC entry 2521 (class 2606 OID 16962)
-- Name: taxon uk_taxon__taxon_code; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.taxon
    ADD CONSTRAINT uk_taxon__taxon_code UNIQUE (taxon_code);


--
-- TOC entry 2523 (class 2606 OID 16964)
-- Name: taxon uk_taxon__taxon_name; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.taxon
    ADD CONSTRAINT uk_taxon__taxon_name UNIQUE (taxon_name);


--
-- TOC entry 2527 (class 2606 OID 16966)
-- Name: user_db uk_user_db__username; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_db
    ADD CONSTRAINT uk_user_db__username UNIQUE (user_name);


--
-- TOC entry 2531 (class 2606 OID 16968)
-- Name: vocabulary uk_vocabulary__parent__code; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vocabulary
    ADD CONSTRAINT uk_vocabulary__parent__code UNIQUE (code, parent);


--
-- TOC entry 2435 (class 1259 OID 16969)
-- Name: idx_1041853b2b63d494; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_1041853b2b63d494 ON public.pcr_is_done_by USING btree (pcr_fk);


--
-- TOC entry 2436 (class 1259 OID 16970)
-- Name: idx_1041853bb53cd04c; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_1041853bb53cd04c ON public.pcr_is_done_by USING btree (person_fk);


--
-- TOC entry 2298 (class 1259 OID 16971)
-- Name: idx_10a697444236d33e; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_10a697444236d33e ON public.composition_of_internal_biological_material USING btree (specimen_type_voc_fk);


--
-- TOC entry 2299 (class 1259 OID 16972)
-- Name: idx_10a6974454dbbd4d; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_10a6974454dbbd4d ON public.composition_of_internal_biological_material USING btree (internal_biological_material_fk);


--
-- TOC entry 2488 (class 1259 OID 16973)
-- Name: idx_16dc6005821b1d3f; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_16dc6005821b1d3f ON public.source_is_entered_by USING btree (source_fk);


--
-- TOC entry 2489 (class 1259 OID 16974)
-- Name: idx_16dc6005b53cd04c; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_16dc6005b53cd04c ON public.source_is_entered_by USING btree (person_fk);


--
-- TOC entry 2409 (class 1259 OID 16975)
-- Name: idx_17a90ea3503b4409; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_17a90ea3503b4409 ON public.motu_is_generated_by USING btree (motu_fk);


--
-- TOC entry 2410 (class 1259 OID 16976)
-- Name: idx_17a90ea3b53cd04c; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_17a90ea3b53cd04c ON public.motu_is_generated_by USING btree (person_fk);


--
-- TOC entry 2463 (class 1259 OID 16977)
-- Name: idx_18fcbb8f662d9b98; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_18fcbb8f662d9b98 ON public.sampling_is_funded_by USING btree (sampling_fk);


--
-- TOC entry 2464 (class 1259 OID 16978)
-- Name: idx_18fcbb8f759c7bb0; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_18fcbb8f759c7bb0 ON public.sampling_is_funded_by USING btree (program_fk);


--
-- TOC entry 2306 (class 1259 OID 16979)
-- Name: idx_1dcf9af9c53b46b; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_1dcf9af9c53b46b ON public.dna USING btree (dna_quality_voc_fk);


--
-- TOC entry 2391 (class 1259 OID 16980)
-- Name: idx_353cf66988085e0f; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_353cf66988085e0f ON public.internal_sequence USING btree (internal_sequence_status_voc_fk);


--
-- TOC entry 2392 (class 1259 OID 16981)
-- Name: idx_353cf669a30c442f; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_353cf669a30c442f ON public.internal_sequence USING btree (date_precision_voc_fk);


--
-- TOC entry 2359 (class 1259 OID 16982)
-- Name: idx_49d19c8d40d80ecd; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_49d19c8d40d80ecd ON public.identified_species USING btree (external_biological_material_fk);


--
-- TOC entry 2360 (class 1259 OID 16983)
-- Name: idx_49d19c8d54dbbd4d; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_49d19c8d54dbbd4d ON public.identified_species USING btree (internal_biological_material_fk);


--
-- TOC entry 2361 (class 1259 OID 16984)
-- Name: idx_49d19c8d5be90e48; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_49d19c8d5be90e48 ON public.identified_species USING btree (internal_sequence_fk);


--
-- TOC entry 2362 (class 1259 OID 16985)
-- Name: idx_49d19c8d5f2c6176; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_49d19c8d5f2c6176 ON public.identified_species USING btree (specimen_fk);


--
-- TOC entry 2363 (class 1259 OID 16986)
-- Name: idx_49d19c8d7b09e3bc; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_49d19c8d7b09e3bc ON public.identified_species USING btree (taxon_fk);


--
-- TOC entry 2364 (class 1259 OID 16987)
-- Name: idx_49d19c8da30c442f; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_49d19c8da30c442f ON public.identified_species USING btree (date_precision_voc_fk);


--
-- TOC entry 2365 (class 1259 OID 16988)
-- Name: idx_49d19c8dcdd1f756; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_49d19c8dcdd1f756 ON public.identified_species USING btree (external_sequence_fk);


--
-- TOC entry 2366 (class 1259 OID 16989)
-- Name: idx_49d19c8dfb5f790; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_49d19c8dfb5f790 ON public.identified_species USING btree (identification_criterion_voc_fk);


--
-- TOC entry 2413 (class 1259 OID 16990)
-- Name: idx_4e79cb8d40e7e0b3; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_4e79cb8d40e7e0b3 ON public.motu_number USING btree (delimitation_method_voc_fk);


--
-- TOC entry 2414 (class 1259 OID 16991)
-- Name: idx_4e79cb8d503b4409; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_4e79cb8d503b4409 ON public.motu_number USING btree (motu_fk);


--
-- TOC entry 2415 (class 1259 OID 16992)
-- Name: idx_4e79cb8d5be90e48; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_4e79cb8d5be90e48 ON public.motu_number USING btree (internal_sequence_fk);


--
-- TOC entry 2416 (class 1259 OID 16993)
-- Name: idx_4e79cb8dcdd1f756; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_4e79cb8dcdd1f756 ON public.motu_number USING btree (external_sequence_fk);


--
-- TOC entry 2452 (class 1259 OID 16994)
-- Name: idx_55ae4a3d369ab36b; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_55ae4a3d369ab36b ON public.sampling USING btree (site_fk);


--
-- TOC entry 2453 (class 1259 OID 16995)
-- Name: idx_55ae4a3d50bb334e; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_55ae4a3d50bb334e ON public.sampling USING btree (donation_voc_fk);


--
-- TOC entry 2454 (class 1259 OID 16996)
-- Name: idx_55ae4a3da30c442f; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_55ae4a3da30c442f ON public.sampling USING btree (date_precision_voc_fk);


--
-- TOC entry 2459 (class 1259 OID 16997)
-- Name: idx_5a6bd88a29b38195; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_5a6bd88a29b38195 ON public.sampling_is_done_with_method USING btree (sampling_method_voc_fk);


--
-- TOC entry 2460 (class 1259 OID 16998)
-- Name: idx_5a6bd88a662d9b98; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_5a6bd88a662d9b98 ON public.sampling_is_done_with_method USING btree (sampling_fk);


--
-- TOC entry 2424 (class 1259 OID 16999)
-- Name: idx_5b6b99362c5b04a7; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_5b6b99362c5b04a7 ON public.pcr USING btree (forward_primer_voc_fk);


--
-- TOC entry 2425 (class 1259 OID 17000)
-- Name: idx_5b6b99364b06319d; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_5b6b99364b06319d ON public.pcr USING btree (dna_fk);


--
-- TOC entry 2426 (class 1259 OID 17001)
-- Name: idx_5b6b99366ccc2566; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_5b6b99366ccc2566 ON public.pcr USING btree (pcr_specificity_voc_fk);


--
-- TOC entry 2427 (class 1259 OID 17002)
-- Name: idx_5b6b99368b4a1710; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_5b6b99368b4a1710 ON public.pcr USING btree (pcr_quality_voc_fk);


--
-- TOC entry 2428 (class 1259 OID 17003)
-- Name: idx_5b6b99369d3cdb05; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_5b6b99369d3cdb05 ON public.pcr USING btree (gene_voc_fk);


--
-- TOC entry 2429 (class 1259 OID 17004)
-- Name: idx_5b6b9936a30c442f; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_5b6b9936a30c442f ON public.pcr USING btree (date_precision_voc_fk);


--
-- TOC entry 2430 (class 1259 OID 17005)
-- Name: idx_5b6b9936f1694267; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_5b6b9936f1694267 ON public.pcr USING btree (reverse_primer_voc_fk);


--
-- TOC entry 2496 (class 1259 OID 17006)
-- Name: idx_5ee42fce4236d33e; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_5ee42fce4236d33e ON public.specimen USING btree (specimen_type_voc_fk);


--
-- TOC entry 2497 (class 1259 OID 17007)
-- Name: idx_5ee42fce54dbbd4d; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_5ee42fce54dbbd4d ON public.specimen USING btree (internal_biological_material_fk);


--
-- TOC entry 2448 (class 1259 OID 17008)
-- Name: idx_60129a315fd841ac; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_60129a315fd841ac ON public.sample_is_fixed_with USING btree (fixative_voc_fk);


--
-- TOC entry 2449 (class 1259 OID 17009)
-- Name: idx_60129a31662d9b98; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_60129a31662d9b98 ON public.sample_is_fixed_with USING btree (sampling_fk);


--
-- TOC entry 2387 (class 1259 OID 17010)
-- Name: idx_69c58aff54dbbd4d; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_69c58aff54dbbd4d ON public.internal_biological_material_is_treated_by USING btree (internal_biological_material_fk);


--
-- TOC entry 2388 (class 1259 OID 17011)
-- Name: idx_69c58affb53cd04c; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_69c58affb53cd04c ON public.internal_biological_material_is_treated_by USING btree (person_fk);


--
-- TOC entry 2511 (class 1259 OID 17012)
-- Name: idx_7718edef41a72d48; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_7718edef41a72d48 ON public.storage_box USING btree (collection_code_voc_fk);


--
-- TOC entry 2512 (class 1259 OID 17013)
-- Name: idx_7718edef57552d30; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_7718edef57552d30 ON public.storage_box USING btree (box_type_voc_fk);


--
-- TOC entry 2513 (class 1259 OID 17014)
-- Name: idx_7718edef9e7b0e1f; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_7718edef9e7b0e1f ON public.storage_box USING btree (collection_type_voc_fk);


--
-- TOC entry 2328 (class 1259 OID 17015)
-- Name: idx_7d78636f40d80ecd; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_7d78636f40d80ecd ON public.external_biological_material_is_processed_by USING btree (external_biological_material_fk);


--
-- TOC entry 2329 (class 1259 OID 17016)
-- Name: idx_7d78636fb53cd04c; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_7d78636fb53cd04c ON public.external_biological_material_is_processed_by USING btree (person_fk);


--
-- TOC entry 2367 (class 1259 OID 17017)
-- Name: idx_801c3911b669f53d; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_801c3911b669f53d ON public.identified_species USING btree (type_material_voc_fk);


--
-- TOC entry 2480 (class 1259 OID 17018)
-- Name: idx_88295540b53cd04c; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_88295540b53cd04c ON public.slide_is_mounted_by USING btree (person_fk);


--
-- TOC entry 2481 (class 1259 OID 17019)
-- Name: idx_88295540d9c85992; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_88295540d9c85992 ON public.slide_is_mounted_by USING btree (specimen_slide_fk);


--
-- TOC entry 2351 (class 1259 OID 17020)
-- Name: idx_8d0e8d6a821b1d3f; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_8d0e8d6a821b1d3f ON public.external_sequence_is_published_in USING btree (source_fk);


--
-- TOC entry 2352 (class 1259 OID 17021)
-- Name: idx_8d0e8d6acdd1f756; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_8d0e8d6acdd1f756 ON public.external_sequence_is_published_in USING btree (external_sequence_fk);


--
-- TOC entry 2504 (class 1259 OID 17022)
-- Name: idx_8da827e22b644673; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_8da827e22b644673 ON public.specimen_slide USING btree (storage_box_fk);


--
-- TOC entry 2505 (class 1259 OID 17023)
-- Name: idx_8da827e25f2c6176; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_8da827e25f2c6176 ON public.specimen_slide USING btree (specimen_fk);


--
-- TOC entry 2506 (class 1259 OID 17024)
-- Name: idx_8da827e2a30c442f; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_8da827e2a30c442f ON public.specimen_slide USING btree (date_precision_voc_fk);


--
-- TOC entry 2336 (class 1259 OID 17025)
-- Name: idx_9e9f85cf514d78e0; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_9e9f85cf514d78e0 ON public.external_sequence USING btree (external_sequence_origin_voc_fk);


--
-- TOC entry 2337 (class 1259 OID 17026)
-- Name: idx_9e9f85cf662d9b98; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_9e9f85cf662d9b98 ON public.external_sequence USING btree (sampling_fk);


--
-- TOC entry 2338 (class 1259 OID 17027)
-- Name: idx_9e9f85cf88085e0f; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_9e9f85cf88085e0f ON public.external_sequence USING btree (external_sequence_status_voc_fk);


--
-- TOC entry 2339 (class 1259 OID 17028)
-- Name: idx_9e9f85cf9d3cdb05; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_9e9f85cf9d3cdb05 ON public.external_sequence USING btree (gene_voc_fk);


--
-- TOC entry 2340 (class 1259 OID 17029)
-- Name: idx_9e9f85cfa30c442f; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_9e9f85cfa30c442f ON public.external_sequence USING btree (date_precision_voc_fk);


--
-- TOC entry 2471 (class 1259 OID 17030)
-- Name: idx_9f39f8b143d4e2c; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_9f39f8b143d4e2c ON public.site USING btree (municipality_fk);


--
-- TOC entry 2472 (class 1259 OID 17031)
-- Name: idx_9f39f8b14d50d031; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_9f39f8b14d50d031 ON public.site USING btree (access_point_voc_fk);


--
-- TOC entry 2473 (class 1259 OID 17032)
-- Name: idx_9f39f8b1b1c3431a; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_9f39f8b1b1c3431a ON public.site USING btree (country_fk);


--
-- TOC entry 2474 (class 1259 OID 17033)
-- Name: idx_9f39f8b1c23046ae; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_9f39f8b1c23046ae ON public.site USING btree (habitat_type_voc_fk);


--
-- TOC entry 2475 (class 1259 OID 17034)
-- Name: idx_9f39f8b1e86dbd90; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_9f39f8b1e86dbd90 ON public.site USING btree (coordinate_precision_voc_fk);


--
-- TOC entry 2315 (class 1259 OID 17035)
-- Name: idx_b786c5214b06319d; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_b786c5214b06319d ON public.dna_is_extracted_by USING btree (dna_fk);


--
-- TOC entry 2316 (class 1259 OID 17036)
-- Name: idx_b786c521b53cd04c; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_b786c521b53cd04c ON public.dna_is_extracted_by USING btree (person_fk);


--
-- TOC entry 2374 (class 1259 OID 17037)
-- Name: idx_ba1841a52b644673; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_ba1841a52b644673 ON public.internal_biological_material USING btree (storage_box_fk);


--
-- TOC entry 2375 (class 1259 OID 17038)
-- Name: idx_ba1841a5662d9b98; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_ba1841a5662d9b98 ON public.internal_biological_material USING btree (sampling_fk);


--
-- TOC entry 2376 (class 1259 OID 17039)
-- Name: idx_ba1841a5a30c442f; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_ba1841a5a30c442f ON public.internal_biological_material USING btree (date_precision_voc_fk);


--
-- TOC entry 2377 (class 1259 OID 17040)
-- Name: idx_ba1841a5a897cc9e; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_ba1841a5a897cc9e ON public.internal_biological_material USING btree (eyes_voc_fk);


--
-- TOC entry 2378 (class 1259 OID 17041)
-- Name: idx_ba1841a5b0b56b73; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_ba1841a5b0b56b73 ON public.internal_biological_material USING btree (pigmentation_voc_fk);


--
-- TOC entry 2403 (class 1259 OID 17042)
-- Name: idx_ba97b9c45be90e48; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_ba97b9c45be90e48 ON public.internal_sequence_is_published_in USING btree (internal_sequence_fk);


--
-- TOC entry 2404 (class 1259 OID 17043)
-- Name: idx_ba97b9c4821b1d3f; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_ba97b9c4821b1d3f ON public.internal_sequence_is_published_in USING btree (source_fk);


--
-- TOC entry 2294 (class 1259 OID 17044)
-- Name: idx_bd45639e5be90e48; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_bd45639e5be90e48 ON public.chromatogram_is_processed_to USING btree (internal_sequence_fk);


--
-- TOC entry 2295 (class 1259 OID 17045)
-- Name: idx_bd45639eefcfd332; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_bd45639eefcfd332 ON public.chromatogram_is_processed_to USING btree (chromatogram_fk);


--
-- TOC entry 2355 (class 1259 OID 17046)
-- Name: idx_c0df0ce4662d9b98; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_c0df0ce4662d9b98 ON public.has_targeted_taxa USING btree (sampling_fk);


--
-- TOC entry 2356 (class 1259 OID 17047)
-- Name: idx_c0df0ce47b09e3bc; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_c0df0ce47b09e3bc ON public.has_targeted_taxa USING btree (taxon_fk);


--
-- TOC entry 2332 (class 1259 OID 17048)
-- Name: idx_d2338bb240d80ecd; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_d2338bb240d80ecd ON public.external_biological_material_is_published_in USING btree (external_biological_material_fk);


--
-- TOC entry 2333 (class 1259 OID 17049)
-- Name: idx_d2338bb2821b1d3f; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_d2338bb2821b1d3f ON public.external_biological_material_is_published_in USING btree (source_fk);


--
-- TOC entry 2347 (class 1259 OID 17050)
-- Name: idx_dc41e25ab53cd04c; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_dc41e25ab53cd04c ON public.external_sequence_is_entered_by USING btree (person_fk);


--
-- TOC entry 2348 (class 1259 OID 17051)
-- Name: idx_dc41e25acdd1f756; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_dc41e25acdd1f756 ON public.external_sequence_is_entered_by USING btree (external_sequence_fk);


--
-- TOC entry 2307 (class 1259 OID 17052)
-- Name: idx_dna__date_precision_voc_fk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_dna__date_precision_voc_fk ON public.dna USING btree (date_precision_voc_fk);


--
-- TOC entry 2308 (class 1259 OID 17053)
-- Name: idx_dna__dna_extraction_method_voc_fk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_dna__dna_extraction_method_voc_fk ON public.dna USING btree (dna_extraction_method_voc_fk);


--
-- TOC entry 2309 (class 1259 OID 17054)
-- Name: idx_dna__specimen_fk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_dna__specimen_fk ON public.dna USING btree (specimen_fk);


--
-- TOC entry 2310 (class 1259 OID 17055)
-- Name: idx_dna__storage_box_fk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_dna__storage_box_fk ON public.dna USING btree (storage_box_fk);


--
-- TOC entry 2419 (class 1259 OID 17056)
-- Name: idx_e2e2d1eeb1c3431a; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_e2e2d1eeb1c3431a ON public.municipality USING btree (country_fk);


--
-- TOC entry 2383 (class 1259 OID 17057)
-- Name: idx_ea07bfa754dbbd4d; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_ea07bfa754dbbd4d ON public.internal_biological_material_is_published_in USING btree (internal_biological_material_fk);


--
-- TOC entry 2384 (class 1259 OID 17058)
-- Name: idx_ea07bfa7821b1d3f; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_ea07bfa7821b1d3f ON public.internal_biological_material_is_published_in USING btree (source_fk);


--
-- TOC entry 2467 (class 1259 OID 17059)
-- Name: idx_ee2a88c9662d9b98; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_ee2a88c9662d9b98 ON public.sampling_is_performed_by USING btree (sampling_fk);


--
-- TOC entry 2468 (class 1259 OID 17060)
-- Name: idx_ee2a88c9b53cd04c; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_ee2a88c9b53cd04c ON public.sampling_is_performed_by USING btree (person_fk);


--
-- TOC entry 2319 (class 1259 OID 17061)
-- Name: idx_eefa43f3662d9b98; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_eefa43f3662d9b98 ON public.external_biological_material USING btree (sampling_fk);


--
-- TOC entry 2320 (class 1259 OID 17062)
-- Name: idx_eefa43f382acdc4; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_eefa43f382acdc4 ON public.external_biological_material USING btree (number_of_specimens_voc_fk);


--
-- TOC entry 2321 (class 1259 OID 17063)
-- Name: idx_eefa43f3a30c442f; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_eefa43f3a30c442f ON public.external_biological_material USING btree (date_precision_voc_fk);


--
-- TOC entry 2322 (class 1259 OID 17064)
-- Name: idx_eefa43f3a897cc9e; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_eefa43f3a897cc9e ON public.external_biological_material USING btree (eyes_voc_fk);


--
-- TOC entry 2323 (class 1259 OID 17065)
-- Name: idx_eefa43f3b0b56b73; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_eefa43f3b0b56b73 ON public.external_biological_material USING btree (pigmentation_voc_fk);


--
-- TOC entry 2399 (class 1259 OID 17066)
-- Name: idx_f6971ba85be90e48; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_f6971ba85be90e48 ON public.internal_sequence_is_assembled_by USING btree (internal_sequence_fk);


--
-- TOC entry 2400 (class 1259 OID 17067)
-- Name: idx_f6971ba8b53cd04c; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_f6971ba8b53cd04c ON public.internal_sequence_is_assembled_by USING btree (person_fk);


--
-- TOC entry 2492 (class 1259 OID 17068)
-- Name: idx_f8fccf63b4ab6ba0; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_f8fccf63b4ab6ba0 ON public.species_is_identified_by USING btree (identified_species_fk);


--
-- TOC entry 2493 (class 1259 OID 17069)
-- Name: idx_f8fccf63b53cd04c; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_f8fccf63b53cd04c ON public.species_is_identified_by USING btree (person_fk);


--
-- TOC entry 2286 (class 1259 OID 17070)
-- Name: idx_fcb2dab7206fe5c0; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_fcb2dab7206fe5c0 ON public.chromatogram USING btree (chromato_quality_voc_fk);


--
-- TOC entry 2287 (class 1259 OID 17071)
-- Name: idx_fcb2dab7286bbca9; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_fcb2dab7286bbca9 ON public.chromatogram USING btree (chromato_primer_voc_fk);


--
-- TOC entry 2288 (class 1259 OID 17072)
-- Name: idx_fcb2dab72b63d494; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_fcb2dab72b63d494 ON public.chromatogram USING btree (pcr_fk);


--
-- TOC entry 2289 (class 1259 OID 17073)
-- Name: idx_fcb2dab7e8441376; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_fcb2dab7e8441376 ON public.chromatogram USING btree (institution_fk);


--
-- TOC entry 2439 (class 1259 OID 17074)
-- Name: idx_fcec9efe8441376; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_fcec9efe8441376 ON public.person USING btree (institution_fk);


--
-- TOC entry 2589 (class 2606 OID 17075)
-- Name: internal_sequence_is_published_in fk_; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_sequence_is_published_in
    ADD CONSTRAINT fk_ FOREIGN KEY (source_fk) REFERENCES public.source(id) ON DELETE CASCADE;


--
-- TOC entry 2590 (class 2606 OID 17080)
-- Name: internal_sequence_is_published_in fk_1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_sequence_is_published_in
    ADD CONSTRAINT fk_1 FOREIGN KEY (internal_sequence_fk) REFERENCES public.internal_sequence(id) ON DELETE CASCADE;


--
-- TOC entry 2628 (class 2606 OID 17085)
-- Name: species_is_identified_by fk_10; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.species_is_identified_by
    ADD CONSTRAINT fk_10 FOREIGN KEY (person_fk) REFERENCES public.person(id);


--
-- TOC entry 2615 (class 2606 OID 17090)
-- Name: sampling_is_funded_by fk_100; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sampling_is_funded_by
    ADD CONSTRAINT fk_100 FOREIGN KEY (program_fk) REFERENCES public.program(id);


--
-- TOC entry 2616 (class 2606 OID 17095)
-- Name: sampling_is_funded_by fk_101; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sampling_is_funded_by
    ADD CONSTRAINT fk_101 FOREIGN KEY (sampling_fk) REFERENCES public.sampling(id) ON DELETE CASCADE;


--
-- TOC entry 2632 (class 2606 OID 17100)
-- Name: specimen_slide fk_102; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.specimen_slide
    ADD CONSTRAINT fk_102 FOREIGN KEY (date_precision_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2633 (class 2606 OID 17105)
-- Name: specimen_slide fk_103; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.specimen_slide
    ADD CONSTRAINT fk_103 FOREIGN KEY (storage_box_fk) REFERENCES public.storage_box(id);


--
-- TOC entry 2634 (class 2606 OID 17110)
-- Name: specimen_slide fk_104; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.specimen_slide
    ADD CONSTRAINT fk_104 FOREIGN KEY (specimen_fk) REFERENCES public.specimen(id);


--
-- TOC entry 2593 (class 2606 OID 17115)
-- Name: motu_number fk_11; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.motu_number
    ADD CONSTRAINT fk_11 FOREIGN KEY (external_sequence_fk) REFERENCES public.external_sequence(id);


--
-- TOC entry 2594 (class 2606 OID 17120)
-- Name: motu_number fk_12; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.motu_number
    ADD CONSTRAINT fk_12 FOREIGN KEY (delimitation_method_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2595 (class 2606 OID 17125)
-- Name: motu_number fk_13; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.motu_number
    ADD CONSTRAINT fk_13 FOREIGN KEY (internal_sequence_fk) REFERENCES public.internal_sequence(id);


--
-- TOC entry 2596 (class 2606 OID 17130)
-- Name: motu_number fk_14; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.motu_number
    ADD CONSTRAINT fk_14 FOREIGN KEY (motu_fk) REFERENCES public.motu(id) ON DELETE CASCADE;


--
-- TOC entry 2538 (class 2606 OID 17135)
-- Name: composition_of_internal_biological_material fk_15; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.composition_of_internal_biological_material
    ADD CONSTRAINT fk_15 FOREIGN KEY (specimen_type_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2539 (class 2606 OID 17140)
-- Name: composition_of_internal_biological_material fk_16; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.composition_of_internal_biological_material
    ADD CONSTRAINT fk_16 FOREIGN KEY (internal_biological_material_fk) REFERENCES public.internal_biological_material(id) ON DELETE CASCADE;


--
-- TOC entry 2565 (class 2606 OID 17145)
-- Name: has_targeted_taxa fk_17; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.has_targeted_taxa
    ADD CONSTRAINT fk_17 FOREIGN KEY (sampling_fk) REFERENCES public.sampling(id) ON DELETE CASCADE;


--
-- TOC entry 2566 (class 2606 OID 17150)
-- Name: has_targeted_taxa fk_18; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.has_targeted_taxa
    ADD CONSTRAINT fk_18 FOREIGN KEY (taxon_fk) REFERENCES public.taxon(id);


--
-- TOC entry 2587 (class 2606 OID 17155)
-- Name: internal_sequence_is_assembled_by fk_19; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_sequence_is_assembled_by
    ADD CONSTRAINT fk_19 FOREIGN KEY (internal_sequence_fk) REFERENCES public.internal_sequence(id) ON DELETE CASCADE;


--
-- TOC entry 2556 (class 2606 OID 17160)
-- Name: external_sequence fk_2; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_sequence
    ADD CONSTRAINT fk_2 FOREIGN KEY (gene_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2588 (class 2606 OID 17165)
-- Name: internal_sequence_is_assembled_by fk_20; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_sequence_is_assembled_by
    ADD CONSTRAINT fk_20 FOREIGN KEY (person_fk) REFERENCES public.person(id);


--
-- TOC entry 2605 (class 2606 OID 17170)
-- Name: pcr_is_done_by fk_21; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pcr_is_done_by
    ADD CONSTRAINT fk_21 FOREIGN KEY (pcr_fk) REFERENCES public.pcr(id) ON DELETE CASCADE;


--
-- TOC entry 2606 (class 2606 OID 17175)
-- Name: pcr_is_done_by fk_22; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pcr_is_done_by
    ADD CONSTRAINT fk_22 FOREIGN KEY (person_fk) REFERENCES public.person(id);


--
-- TOC entry 2545 (class 2606 OID 17180)
-- Name: dna_is_extracted_by fk_23; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dna_is_extracted_by
    ADD CONSTRAINT fk_23 FOREIGN KEY (dna_fk) REFERENCES public.dna(id) ON DELETE CASCADE;


--
-- TOC entry 2546 (class 2606 OID 17185)
-- Name: dna_is_extracted_by fk_24; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dna_is_extracted_by
    ADD CONSTRAINT fk_24 FOREIGN KEY (person_fk) REFERENCES public.person(id);


--
-- TOC entry 2624 (class 2606 OID 17190)
-- Name: slide_is_mounted_by fk_25; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.slide_is_mounted_by
    ADD CONSTRAINT fk_25 FOREIGN KEY (specimen_slide_fk) REFERENCES public.specimen_slide(id) ON DELETE CASCADE;


--
-- TOC entry 2625 (class 2606 OID 17195)
-- Name: slide_is_mounted_by fk_26; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.slide_is_mounted_by
    ADD CONSTRAINT fk_26 FOREIGN KEY (person_fk) REFERENCES public.person(id);


--
-- TOC entry 2583 (class 2606 OID 17200)
-- Name: internal_biological_material_is_treated_by fk_27; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_biological_material_is_treated_by
    ADD CONSTRAINT fk_27 FOREIGN KEY (internal_biological_material_fk) REFERENCES public.internal_biological_material(id) ON DELETE CASCADE;


--
-- TOC entry 2584 (class 2606 OID 17205)
-- Name: internal_biological_material_is_treated_by fk_28; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_biological_material_is_treated_by
    ADD CONSTRAINT fk_28 FOREIGN KEY (person_fk) REFERENCES public.person(id);


--
-- TOC entry 2626 (class 2606 OID 17210)
-- Name: source_is_entered_by fk_29; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.source_is_entered_by
    ADD CONSTRAINT fk_29 FOREIGN KEY (source_fk) REFERENCES public.source(id) ON DELETE CASCADE;


--
-- TOC entry 2557 (class 2606 OID 17215)
-- Name: external_sequence fk_3; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_sequence
    ADD CONSTRAINT fk_3 FOREIGN KEY (date_precision_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2627 (class 2606 OID 17220)
-- Name: source_is_entered_by fk_30; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.source_is_entered_by
    ADD CONSTRAINT fk_30 FOREIGN KEY (person_fk) REFERENCES public.person(id);


--
-- TOC entry 2561 (class 2606 OID 17225)
-- Name: external_sequence_is_entered_by fk_31; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_sequence_is_entered_by
    ADD CONSTRAINT fk_31 FOREIGN KEY (external_sequence_fk) REFERENCES public.external_sequence(id) ON DELETE CASCADE;


--
-- TOC entry 2562 (class 2606 OID 17230)
-- Name: external_sequence_is_entered_by fk_32; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_sequence_is_entered_by
    ADD CONSTRAINT fk_32 FOREIGN KEY (person_fk) REFERENCES public.person(id);


--
-- TOC entry 2591 (class 2606 OID 17235)
-- Name: motu_is_generated_by fk_33; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.motu_is_generated_by
    ADD CONSTRAINT fk_33 FOREIGN KEY (motu_fk) REFERENCES public.motu(id) ON DELETE CASCADE;


--
-- TOC entry 2592 (class 2606 OID 17240)
-- Name: motu_is_generated_by fk_34; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.motu_is_generated_by
    ADD CONSTRAINT fk_34 FOREIGN KEY (person_fk) REFERENCES public.person(id);


--
-- TOC entry 2585 (class 2606 OID 17245)
-- Name: internal_sequence fk_35; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_sequence
    ADD CONSTRAINT fk_35 FOREIGN KEY (date_precision_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2586 (class 2606 OID 17250)
-- Name: internal_sequence fk_36; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_sequence
    ADD CONSTRAINT fk_36 FOREIGN KEY (internal_sequence_status_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2536 (class 2606 OID 17255)
-- Name: chromatogram_is_processed_to fk_37; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.chromatogram_is_processed_to
    ADD CONSTRAINT fk_37 FOREIGN KEY (chromatogram_fk) REFERENCES public.chromatogram(id);


--
-- TOC entry 2537 (class 2606 OID 17260)
-- Name: chromatogram_is_processed_to fk_38; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.chromatogram_is_processed_to
    ADD CONSTRAINT fk_38 FOREIGN KEY (internal_sequence_fk) REFERENCES public.internal_sequence(id) ON DELETE CASCADE;


--
-- TOC entry 2597 (class 2606 OID 17265)
-- Name: municipality fk_39; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.municipality
    ADD CONSTRAINT fk_39 FOREIGN KEY (country_fk) REFERENCES public.country(id);


--
-- TOC entry 2558 (class 2606 OID 17270)
-- Name: external_sequence fk_4; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_sequence
    ADD CONSTRAINT fk_4 FOREIGN KEY (external_sequence_origin_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2613 (class 2606 OID 17275)
-- Name: sampling_is_done_with_method fk_40; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sampling_is_done_with_method
    ADD CONSTRAINT fk_40 FOREIGN KEY (sampling_method_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2614 (class 2606 OID 17280)
-- Name: sampling_is_done_with_method fk_41; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sampling_is_done_with_method
    ADD CONSTRAINT fk_41 FOREIGN KEY (sampling_fk) REFERENCES public.sampling(id) ON DELETE CASCADE;


--
-- TOC entry 2608 (class 2606 OID 17285)
-- Name: sample_is_fixed_with fk_42; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sample_is_fixed_with
    ADD CONSTRAINT fk_42 FOREIGN KEY (fixative_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2609 (class 2606 OID 17290)
-- Name: sample_is_fixed_with fk_43; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sample_is_fixed_with
    ADD CONSTRAINT fk_43 FOREIGN KEY (sampling_fk) REFERENCES public.sampling(id) ON DELETE CASCADE;


--
-- TOC entry 2581 (class 2606 OID 17295)
-- Name: internal_biological_material_is_published_in fk_44; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_biological_material_is_published_in
    ADD CONSTRAINT fk_44 FOREIGN KEY (internal_biological_material_fk) REFERENCES public.internal_biological_material(id) ON DELETE CASCADE;


--
-- TOC entry 2582 (class 2606 OID 17300)
-- Name: internal_biological_material_is_published_in fk_45; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_biological_material_is_published_in
    ADD CONSTRAINT fk_45 FOREIGN KEY (source_fk) REFERENCES public.source(id) ON DELETE CASCADE;


--
-- TOC entry 2547 (class 2606 OID 17305)
-- Name: external_biological_material fk_46; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_biological_material
    ADD CONSTRAINT fk_46 FOREIGN KEY (sampling_fk) REFERENCES public.sampling(id);


--
-- TOC entry 2548 (class 2606 OID 17310)
-- Name: external_biological_material fk_47; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_biological_material
    ADD CONSTRAINT fk_47 FOREIGN KEY (date_precision_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2549 (class 2606 OID 17315)
-- Name: external_biological_material fk_48; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_biological_material
    ADD CONSTRAINT fk_48 FOREIGN KEY (number_of_specimens_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2550 (class 2606 OID 17320)
-- Name: external_biological_material fk_49; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_biological_material
    ADD CONSTRAINT fk_49 FOREIGN KEY (pigmentation_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2559 (class 2606 OID 17325)
-- Name: external_sequence fk_5; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_sequence
    ADD CONSTRAINT fk_5 FOREIGN KEY (sampling_fk) REFERENCES public.sampling(id);


--
-- TOC entry 2551 (class 2606 OID 17330)
-- Name: external_biological_material fk_50; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_biological_material
    ADD CONSTRAINT fk_50 FOREIGN KEY (eyes_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2552 (class 2606 OID 17335)
-- Name: external_biological_material_is_processed_by fk_51; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_biological_material_is_processed_by
    ADD CONSTRAINT fk_51 FOREIGN KEY (person_fk) REFERENCES public.person(id);


--
-- TOC entry 2553 (class 2606 OID 17340)
-- Name: external_biological_material_is_processed_by fk_52; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_biological_material_is_processed_by
    ADD CONSTRAINT fk_52 FOREIGN KEY (external_biological_material_fk) REFERENCES public.external_biological_material(id) ON DELETE CASCADE;


--
-- TOC entry 2554 (class 2606 OID 17345)
-- Name: external_biological_material_is_published_in fk_53; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_biological_material_is_published_in
    ADD CONSTRAINT fk_53 FOREIGN KEY (external_biological_material_fk) REFERENCES public.external_biological_material(id) ON DELETE CASCADE;


--
-- TOC entry 2555 (class 2606 OID 17350)
-- Name: external_biological_material_is_published_in fk_54; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_biological_material_is_published_in
    ADD CONSTRAINT fk_54 FOREIGN KEY (source_fk) REFERENCES public.source(id) ON DELETE CASCADE;


--
-- TOC entry 2619 (class 2606 OID 17355)
-- Name: site fk_55; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.site
    ADD CONSTRAINT fk_55 FOREIGN KEY (municipality_fk) REFERENCES public.municipality(id);


--
-- TOC entry 2620 (class 2606 OID 17360)
-- Name: site fk_56; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.site
    ADD CONSTRAINT fk_56 FOREIGN KEY (country_fk) REFERENCES public.country(id);


--
-- TOC entry 2621 (class 2606 OID 17365)
-- Name: site fk_57; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.site
    ADD CONSTRAINT fk_57 FOREIGN KEY (access_point_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2622 (class 2606 OID 17370)
-- Name: site fk_58; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.site
    ADD CONSTRAINT fk_58 FOREIGN KEY (habitat_type_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2623 (class 2606 OID 17375)
-- Name: site fk_59; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.site
    ADD CONSTRAINT fk_59 FOREIGN KEY (coordinate_precision_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2560 (class 2606 OID 17380)
-- Name: external_sequence fk_6; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_sequence
    ADD CONSTRAINT fk_6 FOREIGN KEY (external_sequence_status_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2610 (class 2606 OID 17385)
-- Name: sampling fk_60; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sampling
    ADD CONSTRAINT fk_60 FOREIGN KEY (date_precision_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2611 (class 2606 OID 17390)
-- Name: sampling fk_61; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sampling
    ADD CONSTRAINT fk_61 FOREIGN KEY (donation_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2612 (class 2606 OID 17395)
-- Name: sampling fk_62; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sampling
    ADD CONSTRAINT fk_62 FOREIGN KEY (site_fk) REFERENCES public.site(id);


--
-- TOC entry 2607 (class 2606 OID 17400)
-- Name: person fk_63; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.person
    ADD CONSTRAINT fk_63 FOREIGN KEY (institution_fk) REFERENCES public.institution(id);


--
-- TOC entry 2617 (class 2606 OID 17405)
-- Name: sampling_is_performed_by fk_64; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sampling_is_performed_by
    ADD CONSTRAINT fk_64 FOREIGN KEY (person_fk) REFERENCES public.person(id);


--
-- TOC entry 2618 (class 2606 OID 17410)
-- Name: sampling_is_performed_by fk_65; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sampling_is_performed_by
    ADD CONSTRAINT fk_65 FOREIGN KEY (sampling_fk) REFERENCES public.sampling(id) ON DELETE CASCADE;


--
-- TOC entry 2635 (class 2606 OID 17415)
-- Name: storage_box fk_66; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.storage_box
    ADD CONSTRAINT fk_66 FOREIGN KEY (collection_type_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2636 (class 2606 OID 17420)
-- Name: storage_box fk_67; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.storage_box
    ADD CONSTRAINT fk_67 FOREIGN KEY (collection_code_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2637 (class 2606 OID 17425)
-- Name: storage_box fk_68; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.storage_box
    ADD CONSTRAINT fk_68 FOREIGN KEY (box_type_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2576 (class 2606 OID 17430)
-- Name: internal_biological_material fk_69; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_biological_material
    ADD CONSTRAINT fk_69 FOREIGN KEY (date_precision_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2563 (class 2606 OID 17435)
-- Name: external_sequence_is_published_in fk_7; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_sequence_is_published_in
    ADD CONSTRAINT fk_7 FOREIGN KEY (source_fk) REFERENCES public.source(id) ON DELETE CASCADE;


--
-- TOC entry 2577 (class 2606 OID 17440)
-- Name: internal_biological_material fk_70; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_biological_material
    ADD CONSTRAINT fk_70 FOREIGN KEY (pigmentation_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2578 (class 2606 OID 17445)
-- Name: internal_biological_material fk_71; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_biological_material
    ADD CONSTRAINT fk_71 FOREIGN KEY (eyes_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2579 (class 2606 OID 17450)
-- Name: internal_biological_material fk_72; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_biological_material
    ADD CONSTRAINT fk_72 FOREIGN KEY (sampling_fk) REFERENCES public.sampling(id);


--
-- TOC entry 2580 (class 2606 OID 17455)
-- Name: internal_biological_material fk_73; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.internal_biological_material
    ADD CONSTRAINT fk_73 FOREIGN KEY (storage_box_fk) REFERENCES public.storage_box(id);


--
-- TOC entry 2567 (class 2606 OID 17460)
-- Name: identified_species fk_74; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.identified_species
    ADD CONSTRAINT fk_74 FOREIGN KEY (identification_criterion_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2568 (class 2606 OID 17465)
-- Name: identified_species fk_75; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.identified_species
    ADD CONSTRAINT fk_75 FOREIGN KEY (date_precision_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2569 (class 2606 OID 17470)
-- Name: identified_species fk_76; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.identified_species
    ADD CONSTRAINT fk_76 FOREIGN KEY (external_sequence_fk) REFERENCES public.external_sequence(id) ON DELETE CASCADE;


--
-- TOC entry 2570 (class 2606 OID 17475)
-- Name: identified_species fk_77; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.identified_species
    ADD CONSTRAINT fk_77 FOREIGN KEY (external_biological_material_fk) REFERENCES public.external_biological_material(id) ON DELETE CASCADE;


--
-- TOC entry 2571 (class 2606 OID 17480)
-- Name: identified_species fk_78; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.identified_species
    ADD CONSTRAINT fk_78 FOREIGN KEY (internal_biological_material_fk) REFERENCES public.internal_biological_material(id) ON DELETE CASCADE;


--
-- TOC entry 2572 (class 2606 OID 17485)
-- Name: identified_species fk_79; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.identified_species
    ADD CONSTRAINT fk_79 FOREIGN KEY (taxon_fk) REFERENCES public.taxon(id);


--
-- TOC entry 2564 (class 2606 OID 17490)
-- Name: external_sequence_is_published_in fk_8; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.external_sequence_is_published_in
    ADD CONSTRAINT fk_8 FOREIGN KEY (external_sequence_fk) REFERENCES public.external_sequence(id) ON DELETE CASCADE;


--
-- TOC entry 2573 (class 2606 OID 17495)
-- Name: identified_species fk_80; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.identified_species
    ADD CONSTRAINT fk_80 FOREIGN KEY (specimen_fk) REFERENCES public.specimen(id) ON DELETE CASCADE;


--
-- TOC entry 2574 (class 2606 OID 17500)
-- Name: identified_species fk_801c3911b669f53d; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.identified_species
    ADD CONSTRAINT fk_801c3911b669f53d FOREIGN KEY (type_material_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2575 (class 2606 OID 17505)
-- Name: identified_species fk_81; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.identified_species
    ADD CONSTRAINT fk_81 FOREIGN KEY (internal_sequence_fk) REFERENCES public.internal_sequence(id) ON DELETE CASCADE;


--
-- TOC entry 2630 (class 2606 OID 17510)
-- Name: specimen fk_82; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.specimen
    ADD CONSTRAINT fk_82 FOREIGN KEY (specimen_type_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2631 (class 2606 OID 17515)
-- Name: specimen fk_83; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.specimen
    ADD CONSTRAINT fk_83 FOREIGN KEY (internal_biological_material_fk) REFERENCES public.internal_biological_material(id);


--
-- TOC entry 2540 (class 2606 OID 17520)
-- Name: dna fk_84; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dna
    ADD CONSTRAINT fk_84 FOREIGN KEY (date_precision_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2541 (class 2606 OID 17525)
-- Name: dna fk_85; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dna
    ADD CONSTRAINT fk_85 FOREIGN KEY (dna_extraction_method_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2542 (class 2606 OID 17530)
-- Name: dna fk_86; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dna
    ADD CONSTRAINT fk_86 FOREIGN KEY (specimen_fk) REFERENCES public.specimen(id);


--
-- TOC entry 2543 (class 2606 OID 17535)
-- Name: dna fk_87; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dna
    ADD CONSTRAINT fk_87 FOREIGN KEY (dna_quality_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2544 (class 2606 OID 17540)
-- Name: dna fk_88; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dna
    ADD CONSTRAINT fk_88 FOREIGN KEY (storage_box_fk) REFERENCES public.storage_box(id);


--
-- TOC entry 2598 (class 2606 OID 17545)
-- Name: pcr fk_89; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pcr
    ADD CONSTRAINT fk_89 FOREIGN KEY (gene_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2629 (class 2606 OID 17550)
-- Name: species_is_identified_by fk_9; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.species_is_identified_by
    ADD CONSTRAINT fk_9 FOREIGN KEY (identified_species_fk) REFERENCES public.identified_species(id) ON DELETE CASCADE;


--
-- TOC entry 2599 (class 2606 OID 17555)
-- Name: pcr fk_90; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pcr
    ADD CONSTRAINT fk_90 FOREIGN KEY (pcr_quality_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2600 (class 2606 OID 17560)
-- Name: pcr fk_91; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pcr
    ADD CONSTRAINT fk_91 FOREIGN KEY (pcr_specificity_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2601 (class 2606 OID 17565)
-- Name: pcr fk_92; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pcr
    ADD CONSTRAINT fk_92 FOREIGN KEY (forward_primer_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2602 (class 2606 OID 17570)
-- Name: pcr fk_93; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pcr
    ADD CONSTRAINT fk_93 FOREIGN KEY (reverse_primer_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2603 (class 2606 OID 17575)
-- Name: pcr fk_94; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pcr
    ADD CONSTRAINT fk_94 FOREIGN KEY (date_precision_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2604 (class 2606 OID 17580)
-- Name: pcr fk_95; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pcr
    ADD CONSTRAINT fk_95 FOREIGN KEY (dna_fk) REFERENCES public.dna(id);


--
-- TOC entry 2532 (class 2606 OID 17585)
-- Name: chromatogram fk_96; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.chromatogram
    ADD CONSTRAINT fk_96 FOREIGN KEY (chromato_primer_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2533 (class 2606 OID 17590)
-- Name: chromatogram fk_97; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.chromatogram
    ADD CONSTRAINT fk_97 FOREIGN KEY (chromato_quality_voc_fk) REFERENCES public.vocabulary(id);


--
-- TOC entry 2534 (class 2606 OID 17595)
-- Name: chromatogram fk_98; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.chromatogram
    ADD CONSTRAINT fk_98 FOREIGN KEY (institution_fk) REFERENCES public.institution(id);


--
-- TOC entry 2535 (class 2606 OID 17600)
-- Name: chromatogram fk_99; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.chromatogram
    ADD CONSTRAINT fk_99 FOREIGN KEY (pcr_fk) REFERENCES public.pcr(id);


--
-- TOC entry 2752 (class 0 OID 0)
-- Dependencies: 9
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2020-07-24 14:58:16

--
-- PostgreSQL database dump complete
--

