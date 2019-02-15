--
-- PostgreSQL database dump
--

-- Dumped from database version 9.4.15
-- Dumped by pg_dump version 10.0

-- Started on 2019-02-08 15:05:21

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- TOC entry 1 (class 3079 OID 11855)
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- TOC entry 2748 (class 0 OID 0)
-- Dependencies: 1
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


--
-- TOC entry 3 (class 3079 OID 66222)
-- Name: cube; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS cube WITH SCHEMA public;


--
-- TOC entry 2749 (class 0 OID 0)
-- Dependencies: 3
-- Name: EXTENSION cube; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION cube IS 'data type for multidimensional cubes';


--
-- TOC entry 2 (class 3079 OID 66294)
-- Name: earthdistance; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS earthdistance WITH SCHEMA public;


--
-- TOC entry 2750 (class 0 OID 0)
-- Dependencies: 2
-- Name: EXTENSION earthdistance; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION earthdistance IS 'calculate great-circle distances on the surface of the Earth';


SET search_path = public, pg_catalog;

SET default_with_oids = false;

--
-- TOC entry 175 (class 1259 OID 67467)
-- Name: a_cibler; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE a_cibler (
    id bigint NOT NULL,
    collecte_fk bigint NOT NULL,
    referentiel_taxon_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 176 (class 1259 OID 67470)
-- Name: a_cibler_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE a_cibler_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2751 (class 0 OID 0)
-- Dependencies: 176
-- Name: a_cibler_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE a_cibler_id_seq OWNED BY a_cibler.id;


--
-- TOC entry 177 (class 1259 OID 67472)
-- Name: a_pour_fixateur; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE a_pour_fixateur (
    id bigint NOT NULL,
    fixateur_voc_fk bigint NOT NULL,
    collecte_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 178 (class 1259 OID 67475)
-- Name: a_pour_fixateur_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE a_pour_fixateur_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2752 (class 0 OID 0)
-- Dependencies: 178
-- Name: a_pour_fixateur_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE a_pour_fixateur_id_seq OWNED BY a_pour_fixateur.id;


--
-- TOC entry 179 (class 1259 OID 67477)
-- Name: a_pour_sampling_method; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE a_pour_sampling_method (
    id bigint NOT NULL,
    sampling_method_voc_fk bigint NOT NULL,
    collecte_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 180 (class 1259 OID 67480)
-- Name: a_pour_sampling_method_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE a_pour_sampling_method_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2753 (class 0 OID 0)
-- Dependencies: 180
-- Name: a_pour_sampling_method_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE a_pour_sampling_method_id_seq OWNED BY a_pour_sampling_method.id;


--
-- TOC entry 181 (class 1259 OID 67482)
-- Name: adn; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE adn (
    id bigint NOT NULL,
    code_adn character varying(255) NOT NULL,
    date_adn date,
    concentration_ng_microlitre double precision,
    commentaire_adn text,
    date_precision_voc_fk bigint NOT NULL,
    methode_extraction_adn_voc_fk bigint NOT NULL,
    individu_fk bigint NOT NULL,
    qualite_adn_voc_fk bigint NOT NULL,
    boite_fk bigint,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 182 (class 1259 OID 67488)
-- Name: adn_est_realise_par; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE adn_est_realise_par (
    id bigint NOT NULL,
    adn_fk bigint NOT NULL,
    personne_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 183 (class 1259 OID 67491)
-- Name: adn_est_realise_par_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE adn_est_realise_par_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2754 (class 0 OID 0)
-- Dependencies: 183
-- Name: adn_est_realise_par_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE adn_est_realise_par_id_seq OWNED BY adn_est_realise_par.id;


--
-- TOC entry 184 (class 1259 OID 67493)
-- Name: adn_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE adn_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2755 (class 0 OID 0)
-- Dependencies: 184
-- Name: adn_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE adn_id_seq OWNED BY adn.id;


--
-- TOC entry 185 (class 1259 OID 67495)
-- Name: assigne; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE assigne (
    id bigint NOT NULL,
    num_motu bigint NOT NULL,
    sequence_assemblee_ext_fk bigint,
    methode_motu_voc_fk bigint NOT NULL,
    sequence_assemblee_fk bigint,
    motu_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 186 (class 1259 OID 67498)
-- Name: assigne_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE assigne_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2756 (class 0 OID 0)
-- Dependencies: 186
-- Name: assigne_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE assigne_id_seq OWNED BY assigne.id;


--
-- TOC entry 187 (class 1259 OID 67500)
-- Name: boite; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE boite (
    id bigint NOT NULL,
    code_boite character varying(255) NOT NULL,
    libelle_boite character varying(1024) NOT NULL,
    commentaire_boite text,
    type_collection_voc_fk bigint NOT NULL,
    code_collection_voc_fk bigint NOT NULL,
    type_boite_voc_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 188 (class 1259 OID 67506)
-- Name: boite_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE boite_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2757 (class 0 OID 0)
-- Dependencies: 188
-- Name: boite_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE boite_id_seq OWNED BY boite.id;


--
-- TOC entry 189 (class 1259 OID 67508)
-- Name: chromatogramme; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE chromatogramme (
    id bigint NOT NULL,
    code_chromato character varying(255) NOT NULL,
    num_yas character varying(255) NOT NULL,
    commentaire_chromato text,
    primer_chromato_voc_fk bigint NOT NULL,
    qualite_chromato_voc_fk bigint NOT NULL,
    etablissement_fk bigint NOT NULL,
    pcr_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 190 (class 1259 OID 67514)
-- Name: chromatogramme_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE chromatogramme_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2758 (class 0 OID 0)
-- Dependencies: 190
-- Name: chromatogramme_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE chromatogramme_id_seq OWNED BY chromatogramme.id;


--
-- TOC entry 191 (class 1259 OID 67516)
-- Name: collecte; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE collecte (
    id bigint NOT NULL,
    code_collecte character varying(255) NOT NULL,
    date_collecte date,
    duree_echantillonnage_mn bigint,
    temperature_c double precision,
    conductivite_micro_sie_cm double precision,
    a_faire smallint NOT NULL,
    commentaire_collecte text,
    date_precision_voc_fk bigint NOT NULL,
    leg_voc_fk bigint NOT NULL,
    station_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 192 (class 1259 OID 67522)
-- Name: collecte_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE collecte_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2759 (class 0 OID 0)
-- Dependencies: 192
-- Name: collecte_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE collecte_id_seq OWNED BY collecte.id;


--
-- TOC entry 193 (class 1259 OID 67524)
-- Name: commune; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE commune (
    id bigint NOT NULL,
    code_commune character varying(255) NOT NULL,
    nom_commune character varying(1024) NOT NULL,
    nom_region character varying(1024) NOT NULL,
    pays_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 194 (class 1259 OID 67530)
-- Name: commune_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE commune_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2760 (class 0 OID 0)
-- Dependencies: 194
-- Name: commune_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE commune_id_seq OWNED BY commune.id;


--
-- TOC entry 195 (class 1259 OID 67532)
-- Name: composition_lot_materiel; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE composition_lot_materiel (
    id bigint NOT NULL,
    nb_individus bigint,
    commentaire_compo_lot_materiel text,
    type_individu_voc_fk bigint NOT NULL,
    lot_materiel_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 196 (class 1259 OID 67538)
-- Name: composition_lot_materiel_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE composition_lot_materiel_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2761 (class 0 OID 0)
-- Dependencies: 196
-- Name: composition_lot_materiel_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE composition_lot_materiel_id_seq OWNED BY composition_lot_materiel.id;


--
-- TOC entry 197 (class 1259 OID 67540)
-- Name: espece_identifiee; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE espece_identifiee (
    id bigint NOT NULL,
    date_identification date,
    commentaire_esp_id text,
    critere_identification_voc_fk bigint NOT NULL,
    date_precision_voc_fk bigint NOT NULL,
    sequence_assemblee_ext_fk bigint,
    lot_materiel_ext_fk bigint,
    lot_materiel_fk bigint,
    referentiel_taxon_fk bigint NOT NULL,
    individu_fk bigint,
    sequence_assemblee_fk bigint,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 198 (class 1259 OID 67546)
-- Name: espece_identifiee_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE espece_identifiee_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2762 (class 0 OID 0)
-- Dependencies: 198
-- Name: espece_identifiee_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE espece_identifiee_id_seq OWNED BY espece_identifiee.id;


--
-- TOC entry 199 (class 1259 OID 67548)
-- Name: est_aligne_et_traite; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE est_aligne_et_traite (
    id bigint NOT NULL,
    chromatogramme_fk bigint NOT NULL,
    sequence_assemblee_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 200 (class 1259 OID 67551)
-- Name: est_aligne_et_traite_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE est_aligne_et_traite_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2763 (class 0 OID 0)
-- Dependencies: 200
-- Name: est_aligne_et_traite_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE est_aligne_et_traite_id_seq OWNED BY est_aligne_et_traite.id;


--
-- TOC entry 201 (class 1259 OID 67553)
-- Name: est_effectue_par; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE est_effectue_par (
    id bigint NOT NULL,
    personne_fk bigint NOT NULL,
    collecte_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 202 (class 1259 OID 67556)
-- Name: est_effectue_par_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE est_effectue_par_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2764 (class 0 OID 0)
-- Dependencies: 202
-- Name: est_effectue_par_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE est_effectue_par_id_seq OWNED BY est_effectue_par.id;


--
-- TOC entry 203 (class 1259 OID 67558)
-- Name: est_finance_par; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE est_finance_par (
    id bigint NOT NULL,
    programme_fk bigint NOT NULL,
    collecte_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 204 (class 1259 OID 67561)
-- Name: est_finance_par_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE est_finance_par_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2765 (class 0 OID 0)
-- Dependencies: 204
-- Name: est_finance_par_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE est_finance_par_id_seq OWNED BY est_finance_par.id;


--
-- TOC entry 205 (class 1259 OID 67563)
-- Name: est_identifie_par; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE est_identifie_par (
    id bigint NOT NULL,
    espece_identifiee_fk bigint NOT NULL,
    personne_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 206 (class 1259 OID 67566)
-- Name: est_identifie_par_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE est_identifie_par_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2766 (class 0 OID 0)
-- Dependencies: 206
-- Name: est_identifie_par_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE est_identifie_par_id_seq OWNED BY est_identifie_par.id;


--
-- TOC entry 207 (class 1259 OID 67568)
-- Name: etablissement; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE etablissement (
    id bigint NOT NULL,
    nom_etablissement character varying(1024) NOT NULL,
    commentaire_etablissement text,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 208 (class 1259 OID 67574)
-- Name: etablissement_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE etablissement_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2767 (class 0 OID 0)
-- Dependencies: 208
-- Name: etablissement_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE etablissement_id_seq OWNED BY etablissement.id;


--
-- TOC entry 209 (class 1259 OID 67576)
-- Name: individu; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE individu (
    id bigint NOT NULL,
    code_ind_biomol character varying(255),
    code_ind_tri_morpho character varying(255) NOT NULL,
    code_tube character varying(255) NOT NULL,
    num_ind_biomol character varying(255),
    commentaire_ind text,
    type_individu_voc_fk bigint NOT NULL,
    lot_materiel_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 210 (class 1259 OID 67582)
-- Name: individu_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE individu_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2768 (class 0 OID 0)
-- Dependencies: 210
-- Name: individu_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE individu_id_seq OWNED BY individu.id;


--
-- TOC entry 211 (class 1259 OID 67584)
-- Name: individu_lame; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE individu_lame (
    id bigint NOT NULL,
    code_lame_coll character varying(255) NOT NULL,
    libelle_lame character varying(1024) NOT NULL,
    date_lame date,
    nom_dossier_photos character varying(1024),
    commentaire_lame text,
    date_precision_voc_fk bigint NOT NULL,
    boite_fk bigint,
    individu_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 212 (class 1259 OID 67590)
-- Name: individu_lame_est_realise_par; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE individu_lame_est_realise_par (
    id bigint NOT NULL,
    individu_lame_fk bigint NOT NULL,
    personne_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 213 (class 1259 OID 67593)
-- Name: individu_lame_est_realise_par_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE individu_lame_est_realise_par_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2769 (class 0 OID 0)
-- Dependencies: 213
-- Name: individu_lame_est_realise_par_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE individu_lame_est_realise_par_id_seq OWNED BY individu_lame_est_realise_par.id;


--
-- TOC entry 214 (class 1259 OID 67595)
-- Name: individu_lame_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE individu_lame_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2770 (class 0 OID 0)
-- Dependencies: 214
-- Name: individu_lame_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE individu_lame_id_seq OWNED BY individu_lame.id;


--
-- TOC entry 215 (class 1259 OID 67597)
-- Name: lot_est_publie_dans; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE lot_est_publie_dans (
    id bigint NOT NULL,
    lot_materiel_fk bigint NOT NULL,
    source_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 216 (class 1259 OID 67600)
-- Name: lot_est_publie_dans_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE lot_est_publie_dans_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2771 (class 0 OID 0)
-- Dependencies: 216
-- Name: lot_est_publie_dans_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE lot_est_publie_dans_id_seq OWNED BY lot_est_publie_dans.id;


--
-- TOC entry 217 (class 1259 OID 67602)
-- Name: lot_materiel; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE lot_materiel (
    id bigint NOT NULL,
    code_lot_materiel character varying(255) NOT NULL,
    date_lot_materiel date,
    commentaire_conseil_sqc text,
    commentaire_lot_materiel text,
    a_faire smallint NOT NULL,
    date_precision_voc_fk bigint NOT NULL,
    pigmentation_voc_fk bigint NOT NULL,
    yeux_voc_fk bigint NOT NULL,
    collecte_fk bigint NOT NULL,
    boite_fk bigint,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 218 (class 1259 OID 67608)
-- Name: lot_materiel_est_realise_par; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE lot_materiel_est_realise_par (
    id bigint NOT NULL,
    lot_materiel_fk bigint NOT NULL,
    personne_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 219 (class 1259 OID 67611)
-- Name: lot_materiel_est_realise_par_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE lot_materiel_est_realise_par_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2772 (class 0 OID 0)
-- Dependencies: 219
-- Name: lot_materiel_est_realise_par_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE lot_materiel_est_realise_par_id_seq OWNED BY lot_materiel_est_realise_par.id;


--
-- TOC entry 220 (class 1259 OID 67613)
-- Name: lot_materiel_ext; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE lot_materiel_ext (
    id bigint NOT NULL,
    code_lot_materiel_ext character varying(255) NOT NULL,
    date_creation_lot_materiel_ext date,
    commentaire_lot_materiel_ext text,
    commentaire_nb_individus text,
    collecte_fk bigint NOT NULL,
    date_precision_voc_fk bigint NOT NULL,
    nb_individus_voc_fk bigint NOT NULL,
    pigmentation_voc_fk bigint NOT NULL,
    yeux_voc_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 221 (class 1259 OID 67619)
-- Name: lot_materiel_ext_est_realise_par; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE lot_materiel_ext_est_realise_par (
    id bigint NOT NULL,
    personne_fk bigint NOT NULL,
    lot_materiel_ext_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 222 (class 1259 OID 67622)
-- Name: lot_materiel_ext_est_realise_par_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE lot_materiel_ext_est_realise_par_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2773 (class 0 OID 0)
-- Dependencies: 222
-- Name: lot_materiel_ext_est_realise_par_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE lot_materiel_ext_est_realise_par_id_seq OWNED BY lot_materiel_ext_est_realise_par.id;


--
-- TOC entry 223 (class 1259 OID 67624)
-- Name: lot_materiel_ext_est_reference_dans; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE lot_materiel_ext_est_reference_dans (
    id bigint NOT NULL,
    lot_materiel_ext_fk bigint NOT NULL,
    source_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 224 (class 1259 OID 67627)
-- Name: lot_materiel_ext_est_reference_dans_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE lot_materiel_ext_est_reference_dans_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2774 (class 0 OID 0)
-- Dependencies: 224
-- Name: lot_materiel_ext_est_reference_dans_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE lot_materiel_ext_est_reference_dans_id_seq OWNED BY lot_materiel_ext_est_reference_dans.id;


--
-- TOC entry 225 (class 1259 OID 67629)
-- Name: lot_materiel_ext_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE lot_materiel_ext_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2775 (class 0 OID 0)
-- Dependencies: 225
-- Name: lot_materiel_ext_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE lot_materiel_ext_id_seq OWNED BY lot_materiel_ext.id;


--
-- TOC entry 226 (class 1259 OID 67631)
-- Name: lot_materiel_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE lot_materiel_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2776 (class 0 OID 0)
-- Dependencies: 226
-- Name: lot_materiel_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE lot_materiel_id_seq OWNED BY lot_materiel.id;


--
-- TOC entry 227 (class 1259 OID 67633)
-- Name: motu; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE motu (
    id bigint NOT NULL,
    nom_fichier_csv character varying(1024) NOT NULL,
    date_motu date NOT NULL,
    commentaire_motu text,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint,
    libelle_motu character varying(255) NOT NULL
);


--
-- TOC entry 228 (class 1259 OID 67639)
-- Name: motu_est_genere_par; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE motu_est_genere_par (
    id bigint NOT NULL,
    motu_fk bigint NOT NULL,
    personne_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 229 (class 1259 OID 67642)
-- Name: motu_est_genere_par_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE motu_est_genere_par_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2777 (class 0 OID 0)
-- Dependencies: 229
-- Name: motu_est_genere_par_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE motu_est_genere_par_id_seq OWNED BY motu_est_genere_par.id;


--
-- TOC entry 230 (class 1259 OID 67644)
-- Name: motu_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE motu_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2778 (class 0 OID 0)
-- Dependencies: 230
-- Name: motu_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE motu_id_seq OWNED BY motu.id;


--
-- TOC entry 231 (class 1259 OID 67646)
-- Name: pays; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE pays (
    id bigint NOT NULL,
    code_pays character varying(255) NOT NULL,
    nom_pays character varying(1024) NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 232 (class 1259 OID 67652)
-- Name: pays_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE pays_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2779 (class 0 OID 0)
-- Dependencies: 232
-- Name: pays_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE pays_id_seq OWNED BY pays.id;


--
-- TOC entry 233 (class 1259 OID 67654)
-- Name: pcr; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE pcr (
    id bigint NOT NULL,
    code_pcr character varying(255) NOT NULL,
    num_pcr character varying(255) NOT NULL,
    date_pcr date,
    detail_pcr text,
    remarque_pcr text,
    gene_voc_fk bigint NOT NULL,
    qualite_pcr_voc_fk bigint NOT NULL,
    specificite_voc_fk bigint NOT NULL,
    primer_pcr_start_voc_fk bigint NOT NULL,
    primer_pcr_end_voc_fk bigint NOT NULL,
    date_precision_voc_fk bigint NOT NULL,
    adn_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 234 (class 1259 OID 67660)
-- Name: pcr_est_realise_par; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE pcr_est_realise_par (
    id bigint NOT NULL,
    pcr_fk bigint NOT NULL,
    personne_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 235 (class 1259 OID 67663)
-- Name: pcr_est_realise_par_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE pcr_est_realise_par_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2780 (class 0 OID 0)
-- Dependencies: 235
-- Name: pcr_est_realise_par_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE pcr_est_realise_par_id_seq OWNED BY pcr_est_realise_par.id;


--
-- TOC entry 236 (class 1259 OID 67665)
-- Name: pcr_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE pcr_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2781 (class 0 OID 0)
-- Dependencies: 236
-- Name: pcr_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE pcr_id_seq OWNED BY pcr.id;


--
-- TOC entry 237 (class 1259 OID 67667)
-- Name: personne; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE personne (
    id bigint NOT NULL,
    nom_personne character varying(255) NOT NULL,
    nom_complet character varying(1024),
    nom_personne_ref character varying(255),
    commentaire_personne text,
    etablissement_fk bigint,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 238 (class 1259 OID 67673)
-- Name: personne_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE personne_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2782 (class 0 OID 0)
-- Dependencies: 238
-- Name: personne_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE personne_id_seq OWNED BY personne.id;


--
-- TOC entry 239 (class 1259 OID 67675)
-- Name: programme; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE programme (
    id bigint NOT NULL,
    code_programme character varying(255) NOT NULL,
    nom_programme character varying(1024) NOT NULL,
    noms_responsables text NOT NULL,
    type_financeur character varying(1024),
    annee_debut bigint,
    annee_fin bigint,
    commentaire_programme text,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 240 (class 1259 OID 67681)
-- Name: programme_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE programme_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2783 (class 0 OID 0)
-- Dependencies: 240
-- Name: programme_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE programme_id_seq OWNED BY programme.id;


--
-- TOC entry 241 (class 1259 OID 67683)
-- Name: referentiel_taxon; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE referentiel_taxon (
    id bigint NOT NULL,
    taxname character varying(255) NOT NULL,
    rank character varying(255) NOT NULL,
    subclass character varying(255),
    ordre character varying(255),
    family character varying(255),
    genus character varying(255),
    species character varying(255),
    subspecies character varying(255),
    validity smallint NOT NULL,
    code_taxon character varying(255) NOT NULL,
    commentaire_ref text,
    clade character varying(255),
    taxname_ref character varying(255),
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 242 (class 1259 OID 67689)
-- Name: referentiel_taxon_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE referentiel_taxon_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2784 (class 0 OID 0)
-- Dependencies: 242
-- Name: referentiel_taxon_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE referentiel_taxon_id_seq OWNED BY referentiel_taxon.id;


--
-- TOC entry 243 (class 1259 OID 67691)
-- Name: sequence_assemblee; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE sequence_assemblee (
    id bigint NOT NULL,
    code_sqc_ass character varying(1024) NOT NULL,
    date_creation_sqc_ass date,
    accession_number character varying(255),
    code_sqc_alignement character varying(1024),
    commentaire_sqc_ass text,
    date_precision_voc_fk bigint NOT NULL,
    statut_sqc_ass_voc_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 244 (class 1259 OID 67697)
-- Name: sequence_assemblee_est_realise_par; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE sequence_assemblee_est_realise_par (
    id bigint NOT NULL,
    sequence_assemblee_fk bigint NOT NULL,
    personne_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 245 (class 1259 OID 67700)
-- Name: sequence_assemblee_est_realise_par_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE sequence_assemblee_est_realise_par_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2785 (class 0 OID 0)
-- Dependencies: 245
-- Name: sequence_assemblee_est_realise_par_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE sequence_assemblee_est_realise_par_id_seq OWNED BY sequence_assemblee_est_realise_par.id;


--
-- TOC entry 246 (class 1259 OID 67702)
-- Name: sequence_assemblee_ext; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE sequence_assemblee_ext (
    id bigint NOT NULL,
    code_sqc_ass_ext character varying(1024) NOT NULL,
    date_creation_sqc_ass_ext date,
    accession_number_sqc_ass_ext character varying(255) NOT NULL,
    code_sqc_ass_ext_alignement character varying(1024),
    num_individu_sqc_ass_ext character varying(255) NOT NULL,
    taxon_origine_sqc_ass_ext character varying(255),
    commentaire_sqc_ass_ext text,
    gene_voc_fk bigint NOT NULL,
    date_precision_voc_fk bigint NOT NULL,
    origine_sqc_ass_ext_voc_fk bigint NOT NULL,
    collecte_fk bigint NOT NULL,
    statut_sqc_ass_voc_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 247 (class 1259 OID 67708)
-- Name: sequence_assemblee_ext_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE sequence_assemblee_ext_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2786 (class 0 OID 0)
-- Dependencies: 247
-- Name: sequence_assemblee_ext_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE sequence_assemblee_ext_id_seq OWNED BY sequence_assemblee_ext.id;


--
-- TOC entry 248 (class 1259 OID 67710)
-- Name: sequence_assemblee_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE sequence_assemblee_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2787 (class 0 OID 0)
-- Dependencies: 248
-- Name: sequence_assemblee_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE sequence_assemblee_id_seq OWNED BY sequence_assemblee.id;


--
-- TOC entry 249 (class 1259 OID 67712)
-- Name: source; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE source (
    id bigint NOT NULL,
    code_source character varying(255) NOT NULL,
    annee_source bigint,
    libelle_source character varying(2048) NOT NULL,
    commentaire_source text,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 250 (class 1259 OID 67718)
-- Name: source_a_ete_integre_par; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE source_a_ete_integre_par (
    id bigint NOT NULL,
    source_fk bigint NOT NULL,
    personne_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 251 (class 1259 OID 67721)
-- Name: source_a_ete_integre_par_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE source_a_ete_integre_par_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2788 (class 0 OID 0)
-- Dependencies: 251
-- Name: source_a_ete_integre_par_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE source_a_ete_integre_par_id_seq OWNED BY source_a_ete_integre_par.id;


--
-- TOC entry 252 (class 1259 OID 67723)
-- Name: source_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE source_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2789 (class 0 OID 0)
-- Dependencies: 252
-- Name: source_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE source_id_seq OWNED BY source.id;


--
-- TOC entry 253 (class 1259 OID 67725)
-- Name: sqc_est_publie_dans; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE sqc_est_publie_dans (
    id bigint NOT NULL,
    source_fk bigint NOT NULL,
    sequence_assemblee_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 254 (class 1259 OID 67728)
-- Name: sqc_est_publie_dans_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE sqc_est_publie_dans_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2790 (class 0 OID 0)
-- Dependencies: 254
-- Name: sqc_est_publie_dans_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE sqc_est_publie_dans_id_seq OWNED BY sqc_est_publie_dans.id;


--
-- TOC entry 255 (class 1259 OID 67730)
-- Name: sqc_ext_est_realise_par; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE sqc_ext_est_realise_par (
    id bigint NOT NULL,
    sequence_assemblee_ext_fk bigint NOT NULL,
    personne_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 256 (class 1259 OID 67733)
-- Name: sqc_ext_est_realise_par_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE sqc_ext_est_realise_par_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2791 (class 0 OID 0)
-- Dependencies: 256
-- Name: sqc_ext_est_realise_par_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE sqc_ext_est_realise_par_id_seq OWNED BY sqc_ext_est_realise_par.id;


--
-- TOC entry 257 (class 1259 OID 67735)
-- Name: sqc_ext_est_reference_dans; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE sqc_ext_est_reference_dans (
    id bigint NOT NULL,
    source_fk bigint NOT NULL,
    sequence_assemblee_ext_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 258 (class 1259 OID 67738)
-- Name: sqc_ext_est_reference_dans_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE sqc_ext_est_reference_dans_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2792 (class 0 OID 0)
-- Dependencies: 258
-- Name: sqc_ext_est_reference_dans_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE sqc_ext_est_reference_dans_id_seq OWNED BY sqc_ext_est_reference_dans.id;


--
-- TOC entry 259 (class 1259 OID 67740)
-- Name: station; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE station (
    id bigint NOT NULL,
    code_station character varying(255) NOT NULL,
    nom_station character varying(1024) NOT NULL,
    lat_deg_dec double precision NOT NULL,
    long_deg_dec double precision NOT NULL,
    altitude_m bigint,
    info_localisation text,
    info_description text,
    commentaire_station text,
    commune_fk bigint NOT NULL,
    pays_fk bigint NOT NULL,
    point_acces_voc_fk bigint NOT NULL,
    habitat_type_voc_fk bigint NOT NULL,
    precision_lat_long_voc_fk bigint NOT NULL,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 260 (class 1259 OID 67746)
-- Name: station_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE station_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2793 (class 0 OID 0)
-- Dependencies: 260
-- Name: station_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE station_id_seq OWNED BY station.id;


--
-- TOC entry 261 (class 1259 OID 67748)
-- Name: user_db; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE user_db (
    id bigint NOT NULL,
    username character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    email character varying(255) DEFAULT NULL::character varying,
    role character varying(255) NOT NULL,
    salt character varying(255) DEFAULT NULL::character varying,
    name character varying(255) NOT NULL,
    institution character varying(255) DEFAULT NULL::character varying,
    date_cre timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    date_maj timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    user_cre bigint,
    user_maj bigint,
    is_active smallint NOT NULL,
    commentaire_user text
);


--
-- TOC entry 262 (class 1259 OID 67759)
-- Name: user_db_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE user_db_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2794 (class 0 OID 0)
-- Dependencies: 262
-- Name: user_db_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE user_db_id_seq OWNED BY user_db.id;


--
-- TOC entry 263 (class 1259 OID 67761)
-- Name: voc; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE voc (
    id bigint NOT NULL,
    code character varying(255) NOT NULL,
    libelle character varying(1024) NOT NULL,
    parent character varying(255) NOT NULL,
    commentaire text,
    date_cre timestamp without time zone,
    date_maj timestamp without time zone,
    user_cre bigint,
    user_maj bigint
);


--
-- TOC entry 264 (class 1259 OID 67767)
-- Name: voc_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE voc_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2795 (class 0 OID 0)
-- Dependencies: 264
-- Name: voc_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE voc_id_seq OWNED BY voc.id;


--
-- TOC entry 2235 (class 2604 OID 67769)
-- Name: a_cibler id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY a_cibler ALTER COLUMN id SET DEFAULT nextval('a_cibler_id_seq'::regclass);


--
-- TOC entry 2236 (class 2604 OID 67770)
-- Name: a_pour_fixateur id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY a_pour_fixateur ALTER COLUMN id SET DEFAULT nextval('a_pour_fixateur_id_seq'::regclass);


--
-- TOC entry 2237 (class 2604 OID 67771)
-- Name: a_pour_sampling_method id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY a_pour_sampling_method ALTER COLUMN id SET DEFAULT nextval('a_pour_sampling_method_id_seq'::regclass);


--
-- TOC entry 2238 (class 2604 OID 67772)
-- Name: adn id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY adn ALTER COLUMN id SET DEFAULT nextval('adn_id_seq'::regclass);


--
-- TOC entry 2239 (class 2604 OID 67773)
-- Name: adn_est_realise_par id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY adn_est_realise_par ALTER COLUMN id SET DEFAULT nextval('adn_est_realise_par_id_seq'::regclass);


--
-- TOC entry 2240 (class 2604 OID 67774)
-- Name: assigne id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY assigne ALTER COLUMN id SET DEFAULT nextval('assigne_id_seq'::regclass);


--
-- TOC entry 2241 (class 2604 OID 67775)
-- Name: boite id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY boite ALTER COLUMN id SET DEFAULT nextval('boite_id_seq'::regclass);


--
-- TOC entry 2242 (class 2604 OID 67776)
-- Name: chromatogramme id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY chromatogramme ALTER COLUMN id SET DEFAULT nextval('chromatogramme_id_seq'::regclass);


--
-- TOC entry 2243 (class 2604 OID 67777)
-- Name: collecte id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY collecte ALTER COLUMN id SET DEFAULT nextval('collecte_id_seq'::regclass);


--
-- TOC entry 2244 (class 2604 OID 67778)
-- Name: commune id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY commune ALTER COLUMN id SET DEFAULT nextval('commune_id_seq'::regclass);


--
-- TOC entry 2245 (class 2604 OID 67779)
-- Name: composition_lot_materiel id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY composition_lot_materiel ALTER COLUMN id SET DEFAULT nextval('composition_lot_materiel_id_seq'::regclass);


--
-- TOC entry 2246 (class 2604 OID 67780)
-- Name: espece_identifiee id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY espece_identifiee ALTER COLUMN id SET DEFAULT nextval('espece_identifiee_id_seq'::regclass);


--
-- TOC entry 2247 (class 2604 OID 67781)
-- Name: est_aligne_et_traite id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY est_aligne_et_traite ALTER COLUMN id SET DEFAULT nextval('est_aligne_et_traite_id_seq'::regclass);


--
-- TOC entry 2248 (class 2604 OID 67782)
-- Name: est_effectue_par id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY est_effectue_par ALTER COLUMN id SET DEFAULT nextval('est_effectue_par_id_seq'::regclass);


--
-- TOC entry 2249 (class 2604 OID 67783)
-- Name: est_finance_par id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY est_finance_par ALTER COLUMN id SET DEFAULT nextval('est_finance_par_id_seq'::regclass);


--
-- TOC entry 2250 (class 2604 OID 67784)
-- Name: est_identifie_par id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY est_identifie_par ALTER COLUMN id SET DEFAULT nextval('est_identifie_par_id_seq'::regclass);


--
-- TOC entry 2251 (class 2604 OID 67785)
-- Name: etablissement id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY etablissement ALTER COLUMN id SET DEFAULT nextval('etablissement_id_seq'::regclass);


--
-- TOC entry 2252 (class 2604 OID 67786)
-- Name: individu id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY individu ALTER COLUMN id SET DEFAULT nextval('individu_id_seq'::regclass);


--
-- TOC entry 2253 (class 2604 OID 67787)
-- Name: individu_lame id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY individu_lame ALTER COLUMN id SET DEFAULT nextval('individu_lame_id_seq'::regclass);


--
-- TOC entry 2254 (class 2604 OID 67788)
-- Name: individu_lame_est_realise_par id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY individu_lame_est_realise_par ALTER COLUMN id SET DEFAULT nextval('individu_lame_est_realise_par_id_seq'::regclass);


--
-- TOC entry 2255 (class 2604 OID 67789)
-- Name: lot_est_publie_dans id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_est_publie_dans ALTER COLUMN id SET DEFAULT nextval('lot_est_publie_dans_id_seq'::regclass);


--
-- TOC entry 2256 (class 2604 OID 67790)
-- Name: lot_materiel id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel ALTER COLUMN id SET DEFAULT nextval('lot_materiel_id_seq'::regclass);


--
-- TOC entry 2257 (class 2604 OID 67791)
-- Name: lot_materiel_est_realise_par id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel_est_realise_par ALTER COLUMN id SET DEFAULT nextval('lot_materiel_est_realise_par_id_seq'::regclass);


--
-- TOC entry 2258 (class 2604 OID 67792)
-- Name: lot_materiel_ext id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel_ext ALTER COLUMN id SET DEFAULT nextval('lot_materiel_ext_id_seq'::regclass);


--
-- TOC entry 2259 (class 2604 OID 67793)
-- Name: lot_materiel_ext_est_realise_par id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel_ext_est_realise_par ALTER COLUMN id SET DEFAULT nextval('lot_materiel_ext_est_realise_par_id_seq'::regclass);


--
-- TOC entry 2260 (class 2604 OID 67794)
-- Name: lot_materiel_ext_est_reference_dans id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel_ext_est_reference_dans ALTER COLUMN id SET DEFAULT nextval('lot_materiel_ext_est_reference_dans_id_seq'::regclass);


--
-- TOC entry 2261 (class 2604 OID 67795)
-- Name: motu id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY motu ALTER COLUMN id SET DEFAULT nextval('motu_id_seq'::regclass);


--
-- TOC entry 2262 (class 2604 OID 67796)
-- Name: motu_est_genere_par id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY motu_est_genere_par ALTER COLUMN id SET DEFAULT nextval('motu_est_genere_par_id_seq'::regclass);


--
-- TOC entry 2263 (class 2604 OID 67797)
-- Name: pays id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY pays ALTER COLUMN id SET DEFAULT nextval('pays_id_seq'::regclass);


--
-- TOC entry 2264 (class 2604 OID 67798)
-- Name: pcr id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY pcr ALTER COLUMN id SET DEFAULT nextval('pcr_id_seq'::regclass);


--
-- TOC entry 2265 (class 2604 OID 67799)
-- Name: pcr_est_realise_par id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY pcr_est_realise_par ALTER COLUMN id SET DEFAULT nextval('pcr_est_realise_par_id_seq'::regclass);


--
-- TOC entry 2266 (class 2604 OID 67800)
-- Name: personne id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY personne ALTER COLUMN id SET DEFAULT nextval('personne_id_seq'::regclass);


--
-- TOC entry 2267 (class 2604 OID 67801)
-- Name: programme id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY programme ALTER COLUMN id SET DEFAULT nextval('programme_id_seq'::regclass);


--
-- TOC entry 2268 (class 2604 OID 67802)
-- Name: referentiel_taxon id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY referentiel_taxon ALTER COLUMN id SET DEFAULT nextval('referentiel_taxon_id_seq'::regclass);


--
-- TOC entry 2269 (class 2604 OID 67803)
-- Name: sequence_assemblee id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY sequence_assemblee ALTER COLUMN id SET DEFAULT nextval('sequence_assemblee_id_seq'::regclass);


--
-- TOC entry 2270 (class 2604 OID 67804)
-- Name: sequence_assemblee_est_realise_par id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY sequence_assemblee_est_realise_par ALTER COLUMN id SET DEFAULT nextval('sequence_assemblee_est_realise_par_id_seq'::regclass);


--
-- TOC entry 2271 (class 2604 OID 67805)
-- Name: sequence_assemblee_ext id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY sequence_assemblee_ext ALTER COLUMN id SET DEFAULT nextval('sequence_assemblee_ext_id_seq'::regclass);


--
-- TOC entry 2272 (class 2604 OID 67806)
-- Name: source id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY source ALTER COLUMN id SET DEFAULT nextval('source_id_seq'::regclass);


--
-- TOC entry 2273 (class 2604 OID 67807)
-- Name: source_a_ete_integre_par id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY source_a_ete_integre_par ALTER COLUMN id SET DEFAULT nextval('source_a_ete_integre_par_id_seq'::regclass);


--
-- TOC entry 2274 (class 2604 OID 67808)
-- Name: sqc_est_publie_dans id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY sqc_est_publie_dans ALTER COLUMN id SET DEFAULT nextval('sqc_est_publie_dans_id_seq'::regclass);


--
-- TOC entry 2275 (class 2604 OID 67809)
-- Name: sqc_ext_est_realise_par id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY sqc_ext_est_realise_par ALTER COLUMN id SET DEFAULT nextval('sqc_ext_est_realise_par_id_seq'::regclass);


--
-- TOC entry 2276 (class 2604 OID 67810)
-- Name: sqc_ext_est_reference_dans id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY sqc_ext_est_reference_dans ALTER COLUMN id SET DEFAULT nextval('sqc_ext_est_reference_dans_id_seq'::regclass);


--
-- TOC entry 2277 (class 2604 OID 67811)
-- Name: station id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY station ALTER COLUMN id SET DEFAULT nextval('station_id_seq'::regclass);


--
-- TOC entry 2283 (class 2604 OID 67812)
-- Name: user_db id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY user_db ALTER COLUMN id SET DEFAULT nextval('user_db_id_seq'::regclass);


--
-- TOC entry 2284 (class 2604 OID 67813)
-- Name: voc id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY voc ALTER COLUMN id SET DEFAULT nextval('voc_id_seq'::regclass);


--
-- TOC entry 2286 (class 2606 OID 67815)
-- Name: a_cibler cp_a_cibler_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY a_cibler
    ADD CONSTRAINT cp_a_cibler_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2290 (class 2606 OID 67817)
-- Name: a_pour_fixateur cp_a_pour_fixateur_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY a_pour_fixateur
    ADD CONSTRAINT cp_a_pour_fixateur_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2294 (class 2606 OID 67819)
-- Name: a_pour_sampling_method cp_a_pour_sampling_method_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY a_pour_sampling_method
    ADD CONSTRAINT cp_a_pour_sampling_method_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2302 (class 2606 OID 67821)
-- Name: adn cp_adn_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY adn
    ADD CONSTRAINT cp_adn_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2306 (class 2606 OID 67823)
-- Name: adn_est_realise_par cp_adn_est_realise_par_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY adn_est_realise_par
    ADD CONSTRAINT cp_adn_est_realise_par_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2310 (class 2606 OID 67825)
-- Name: assigne cp_assigne_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY assigne
    ADD CONSTRAINT cp_assigne_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2316 (class 2606 OID 67827)
-- Name: boite cp_boite_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY boite
    ADD CONSTRAINT cp_boite_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2323 (class 2606 OID 67829)
-- Name: chromatogramme cp_chromatogramme_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY chromatogramme
    ADD CONSTRAINT cp_chromatogramme_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2331 (class 2606 OID 67831)
-- Name: collecte cp_collecte_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY collecte
    ADD CONSTRAINT cp_collecte_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2338 (class 2606 OID 67833)
-- Name: commune cp_commune_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY commune
    ADD CONSTRAINT cp_commune_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2343 (class 2606 OID 67835)
-- Name: composition_lot_materiel cp_composition_lot_materiel_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY composition_lot_materiel
    ADD CONSTRAINT cp_composition_lot_materiel_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2347 (class 2606 OID 67837)
-- Name: espece_identifiee cp_espece_identifiee_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY espece_identifiee
    ADD CONSTRAINT cp_espece_identifiee_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2357 (class 2606 OID 67839)
-- Name: est_aligne_et_traite cp_est_aligne_et_traite_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY est_aligne_et_traite
    ADD CONSTRAINT cp_est_aligne_et_traite_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2361 (class 2606 OID 67841)
-- Name: est_effectue_par cp_est_effectue_par_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY est_effectue_par
    ADD CONSTRAINT cp_est_effectue_par_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2365 (class 2606 OID 67843)
-- Name: est_finance_par cp_est_finance_par_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY est_finance_par
    ADD CONSTRAINT cp_est_finance_par_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2369 (class 2606 OID 67845)
-- Name: est_identifie_par cp_est_identifie_par_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY est_identifie_par
    ADD CONSTRAINT cp_est_identifie_par_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2373 (class 2606 OID 67847)
-- Name: etablissement cp_etablissement_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY etablissement
    ADD CONSTRAINT cp_etablissement_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2377 (class 2606 OID 67849)
-- Name: individu cp_individu_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY individu
    ADD CONSTRAINT cp_individu_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2385 (class 2606 OID 67851)
-- Name: individu_lame cp_individu_lame_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY individu_lame
    ADD CONSTRAINT cp_individu_lame_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2392 (class 2606 OID 67853)
-- Name: individu_lame_est_realise_par cp_individu_lame_est_realise_par_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY individu_lame_est_realise_par
    ADD CONSTRAINT cp_individu_lame_est_realise_par_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2396 (class 2606 OID 67855)
-- Name: lot_est_publie_dans cp_lot_est_publie_dans_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_est_publie_dans
    ADD CONSTRAINT cp_lot_est_publie_dans_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2398 (class 2606 OID 67857)
-- Name: lot_materiel cp_lot_materiel_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel
    ADD CONSTRAINT cp_lot_materiel_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2407 (class 2606 OID 67859)
-- Name: lot_materiel_est_realise_par cp_lot_materiel_est_realise_par_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel_est_realise_par
    ADD CONSTRAINT cp_lot_materiel_est_realise_par_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2411 (class 2606 OID 67861)
-- Name: lot_materiel_ext cp_lot_materiel_ext_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel_ext
    ADD CONSTRAINT cp_lot_materiel_ext_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2420 (class 2606 OID 67863)
-- Name: lot_materiel_ext_est_realise_par cp_lot_materiel_ext_est_realise_par_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel_ext_est_realise_par
    ADD CONSTRAINT cp_lot_materiel_ext_est_realise_par_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2424 (class 2606 OID 67865)
-- Name: lot_materiel_ext_est_reference_dans cp_lot_materiel_ext_est_reference_dans_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel_ext_est_reference_dans
    ADD CONSTRAINT cp_lot_materiel_ext_est_reference_dans_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2428 (class 2606 OID 67867)
-- Name: motu cp_motu_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY motu
    ADD CONSTRAINT cp_motu_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2430 (class 2606 OID 67869)
-- Name: motu_est_genere_par cp_motu_est_genere_par_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY motu_est_genere_par
    ADD CONSTRAINT cp_motu_est_genere_par_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2434 (class 2606 OID 67871)
-- Name: pays cp_pays_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY pays
    ADD CONSTRAINT cp_pays_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2438 (class 2606 OID 67873)
-- Name: pcr cp_pcr_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY pcr
    ADD CONSTRAINT cp_pcr_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2449 (class 2606 OID 67875)
-- Name: pcr_est_realise_par cp_pcr_est_realise_par_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY pcr_est_realise_par
    ADD CONSTRAINT cp_pcr_est_realise_par_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2453 (class 2606 OID 67877)
-- Name: personne cp_personne_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY personne
    ADD CONSTRAINT cp_personne_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2458 (class 2606 OID 67879)
-- Name: programme cp_programme_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY programme
    ADD CONSTRAINT cp_programme_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2462 (class 2606 OID 67881)
-- Name: referentiel_taxon cp_referentiel_taxon_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY referentiel_taxon
    ADD CONSTRAINT cp_referentiel_taxon_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2468 (class 2606 OID 67883)
-- Name: sequence_assemblee cp_sequence_assemblee_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sequence_assemblee
    ADD CONSTRAINT cp_sequence_assemblee_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2476 (class 2606 OID 67885)
-- Name: sequence_assemblee_est_realise_par cp_sequence_assemblee_est_realise_par_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sequence_assemblee_est_realise_par
    ADD CONSTRAINT cp_sequence_assemblee_est_realise_par_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2480 (class 2606 OID 67887)
-- Name: sequence_assemblee_ext cp_sequence_assemblee_ext_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sequence_assemblee_ext
    ADD CONSTRAINT cp_sequence_assemblee_ext_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2495 (class 2606 OID 67889)
-- Name: source_a_ete_integre_par cp_source_a_ete_integre_par_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY source_a_ete_integre_par
    ADD CONSTRAINT cp_source_a_ete_integre_par_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2491 (class 2606 OID 67891)
-- Name: source cp_source_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY source
    ADD CONSTRAINT cp_source_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2499 (class 2606 OID 67893)
-- Name: sqc_est_publie_dans cp_sqc_est_publie_dans_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sqc_est_publie_dans
    ADD CONSTRAINT cp_sqc_est_publie_dans_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2503 (class 2606 OID 67895)
-- Name: sqc_ext_est_realise_par cp_sqc_ext_est_realise_par_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sqc_ext_est_realise_par
    ADD CONSTRAINT cp_sqc_ext_est_realise_par_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2507 (class 2606 OID 67897)
-- Name: sqc_ext_est_reference_dans cp_sqc_ext_est_reference_dans_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sqc_ext_est_reference_dans
    ADD CONSTRAINT cp_sqc_ext_est_reference_dans_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2511 (class 2606 OID 67899)
-- Name: station cp_station_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY station
    ADD CONSTRAINT cp_station_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2524 (class 2606 OID 67901)
-- Name: voc cp_voc_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY voc
    ADD CONSTRAINT cp_voc_cle_primaire PRIMARY KEY (id);


--
-- TOC entry 2304 (class 2606 OID 67903)
-- Name: adn cu_adn_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY adn
    ADD CONSTRAINT cu_adn_cle_primaire UNIQUE (code_adn);


--
-- TOC entry 2318 (class 2606 OID 67905)
-- Name: boite cu_boite_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY boite
    ADD CONSTRAINT cu_boite_cle_primaire UNIQUE (code_boite);


--
-- TOC entry 2325 (class 2606 OID 67907)
-- Name: chromatogramme cu_chromatogramme_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY chromatogramme
    ADD CONSTRAINT cu_chromatogramme_cle_primaire UNIQUE (code_chromato);


--
-- TOC entry 2333 (class 2606 OID 67909)
-- Name: collecte cu_collecte_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY collecte
    ADD CONSTRAINT cu_collecte_cle_primaire UNIQUE (code_collecte);


--
-- TOC entry 2340 (class 2606 OID 67911)
-- Name: commune cu_commune_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY commune
    ADD CONSTRAINT cu_commune_cle_primaire UNIQUE (code_commune);


--
-- TOC entry 2375 (class 2606 OID 67913)
-- Name: etablissement cu_etablissement_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY etablissement
    ADD CONSTRAINT cu_etablissement_cle_primaire UNIQUE (nom_etablissement);


--
-- TOC entry 2379 (class 2606 OID 67915)
-- Name: individu cu_individu_code_ind_biomol; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY individu
    ADD CONSTRAINT cu_individu_code_ind_biomol UNIQUE (code_ind_biomol);


--
-- TOC entry 2381 (class 2606 OID 67917)
-- Name: individu cu_individu_code_ind_tri_morpho; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY individu
    ADD CONSTRAINT cu_individu_code_ind_tri_morpho UNIQUE (code_ind_tri_morpho);


--
-- TOC entry 2387 (class 2606 OID 67919)
-- Name: individu_lame cu_individu_lame_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY individu_lame
    ADD CONSTRAINT cu_individu_lame_cle_primaire UNIQUE (code_lame_coll);


--
-- TOC entry 2400 (class 2606 OID 67921)
-- Name: lot_materiel cu_lot_materiel_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel
    ADD CONSTRAINT cu_lot_materiel_cle_primaire UNIQUE (code_lot_materiel);


--
-- TOC entry 2413 (class 2606 OID 68617)
-- Name: lot_materiel_ext cu_lot_materiel_ext_code_lot_materiel_ext; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel_ext
    ADD CONSTRAINT cu_lot_materiel_ext_code_lot_materiel_ext UNIQUE (code_lot_materiel_ext);


--
-- TOC entry 2436 (class 2606 OID 67923)
-- Name: pays cu_pays_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY pays
    ADD CONSTRAINT cu_pays_cle_primaire UNIQUE (code_pays);


--
-- TOC entry 2440 (class 2606 OID 67925)
-- Name: pcr cu_pcr_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY pcr
    ADD CONSTRAINT cu_pcr_cle_primaire UNIQUE (code_pcr);


--
-- TOC entry 2455 (class 2606 OID 67927)
-- Name: personne cu_personne_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY personne
    ADD CONSTRAINT cu_personne_cle_primaire UNIQUE (nom_personne);


--
-- TOC entry 2460 (class 2606 OID 67929)
-- Name: programme cu_programme_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY programme
    ADD CONSTRAINT cu_programme_cle_primaire UNIQUE (code_programme);


--
-- TOC entry 2464 (class 2606 OID 67931)
-- Name: referentiel_taxon cu_referentiel_taxon_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY referentiel_taxon
    ADD CONSTRAINT cu_referentiel_taxon_cle_primaire UNIQUE (taxname);


--
-- TOC entry 2466 (class 2606 OID 67933)
-- Name: referentiel_taxon cu_referentiel_taxon_code_taxon; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY referentiel_taxon
    ADD CONSTRAINT cu_referentiel_taxon_code_taxon UNIQUE (code_taxon);


--
-- TOC entry 2470 (class 2606 OID 67935)
-- Name: sequence_assemblee cu_sequence_assemblee_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sequence_assemblee
    ADD CONSTRAINT cu_sequence_assemblee_cle_primaire UNIQUE (code_sqc_ass);


--
-- TOC entry 2472 (class 2606 OID 67937)
-- Name: sequence_assemblee cu_sequence_assemblee_code_sqc_alignement; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sequence_assemblee
    ADD CONSTRAINT cu_sequence_assemblee_code_sqc_alignement UNIQUE (code_sqc_alignement);


--
-- TOC entry 2482 (class 2606 OID 67939)
-- Name: sequence_assemblee_ext cu_sequence_assemblee_ext_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sequence_assemblee_ext
    ADD CONSTRAINT cu_sequence_assemblee_ext_cle_primaire UNIQUE (code_sqc_ass_ext);


--
-- TOC entry 2484 (class 2606 OID 67941)
-- Name: sequence_assemblee_ext cu_sequence_assemblee_ext_code_sqc_ass_ext_alignement; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sequence_assemblee_ext
    ADD CONSTRAINT cu_sequence_assemblee_ext_code_sqc_ass_ext_alignement UNIQUE (code_sqc_ass_ext_alignement);


--
-- TOC entry 2493 (class 2606 OID 67943)
-- Name: source cu_source_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY source
    ADD CONSTRAINT cu_source_cle_primaire UNIQUE (code_source);


--
-- TOC entry 2513 (class 2606 OID 67945)
-- Name: station cu_station_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY station
    ADD CONSTRAINT cu_station_cle_primaire UNIQUE (code_station);


--
-- TOC entry 2520 (class 2606 OID 68619)
-- Name: user_db cu_user_db_username; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY user_db
    ADD CONSTRAINT cu_user_db_username UNIQUE (username);


--
-- TOC entry 2526 (class 2606 OID 67947)
-- Name: voc cu_voc_cle_primaire; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY voc
    ADD CONSTRAINT cu_voc_cle_primaire UNIQUE (code, parent);


--
-- TOC entry 2522 (class 2606 OID 67949)
-- Name: user_db user_db_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY user_db
    ADD CONSTRAINT user_db_pkey PRIMARY KEY (id);


--
-- TOC entry 2297 (class 1259 OID 67950)
-- Name: cle_etrangere; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cle_etrangere ON adn USING btree (methode_extraction_adn_voc_fk);


--
-- TOC entry 2298 (class 1259 OID 67951)
-- Name: cle_etrangere1; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cle_etrangere1 ON adn USING btree (date_precision_voc_fk);


--
-- TOC entry 2299 (class 1259 OID 67952)
-- Name: cle_etrangere2; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cle_etrangere2 ON adn USING btree (boite_fk);


--
-- TOC entry 2300 (class 1259 OID 67953)
-- Name: cle_etrangere3; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cle_etrangere3 ON adn USING btree (individu_fk);


--
-- TOC entry 2450 (class 1259 OID 67954)
-- Name: idx_1041853b2b63d494; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_1041853b2b63d494 ON pcr_est_realise_par USING btree (pcr_fk);


--
-- TOC entry 2451 (class 1259 OID 67955)
-- Name: idx_1041853bb53cd04c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_1041853bb53cd04c ON pcr_est_realise_par USING btree (personne_fk);


--
-- TOC entry 2344 (class 1259 OID 67956)
-- Name: idx_10a697444236d33e; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_10a697444236d33e ON composition_lot_materiel USING btree (type_individu_voc_fk);


--
-- TOC entry 2345 (class 1259 OID 67957)
-- Name: idx_10a6974454dbbd4d; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_10a6974454dbbd4d ON composition_lot_materiel USING btree (lot_materiel_fk);


--
-- TOC entry 2496 (class 1259 OID 67958)
-- Name: idx_16dc6005821b1d3f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_16dc6005821b1d3f ON source_a_ete_integre_par USING btree (source_fk);


--
-- TOC entry 2497 (class 1259 OID 67959)
-- Name: idx_16dc6005b53cd04c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_16dc6005b53cd04c ON source_a_ete_integre_par USING btree (personne_fk);


--
-- TOC entry 2431 (class 1259 OID 67960)
-- Name: idx_17a90ea3503b4409; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_17a90ea3503b4409 ON motu_est_genere_par USING btree (motu_fk);


--
-- TOC entry 2432 (class 1259 OID 67961)
-- Name: idx_17a90ea3b53cd04c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_17a90ea3b53cd04c ON motu_est_genere_par USING btree (personne_fk);


--
-- TOC entry 2366 (class 1259 OID 67962)
-- Name: idx_18fcbb8f662d9b98; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_18fcbb8f662d9b98 ON est_finance_par USING btree (collecte_fk);


--
-- TOC entry 2367 (class 1259 OID 67963)
-- Name: idx_18fcbb8f759c7bb0; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_18fcbb8f759c7bb0 ON est_finance_par USING btree (programme_fk);


--
-- TOC entry 2473 (class 1259 OID 67964)
-- Name: idx_353cf66988085e0f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_353cf66988085e0f ON sequence_assemblee USING btree (statut_sqc_ass_voc_fk);


--
-- TOC entry 2474 (class 1259 OID 67965)
-- Name: idx_353cf669a30c442f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_353cf669a30c442f ON sequence_assemblee USING btree (date_precision_voc_fk);


--
-- TOC entry 2348 (class 1259 OID 67966)
-- Name: idx_49d19c8d40d80ecd; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_49d19c8d40d80ecd ON espece_identifiee USING btree (lot_materiel_ext_fk);


--
-- TOC entry 2349 (class 1259 OID 67967)
-- Name: idx_49d19c8d54dbbd4d; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_49d19c8d54dbbd4d ON espece_identifiee USING btree (lot_materiel_fk);


--
-- TOC entry 2350 (class 1259 OID 67968)
-- Name: idx_49d19c8d5be90e48; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_49d19c8d5be90e48 ON espece_identifiee USING btree (sequence_assemblee_fk);


--
-- TOC entry 2351 (class 1259 OID 67969)
-- Name: idx_49d19c8d5f2c6176; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_49d19c8d5f2c6176 ON espece_identifiee USING btree (individu_fk);


--
-- TOC entry 2352 (class 1259 OID 67970)
-- Name: idx_49d19c8d7b09e3bc; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_49d19c8d7b09e3bc ON espece_identifiee USING btree (referentiel_taxon_fk);


--
-- TOC entry 2353 (class 1259 OID 67971)
-- Name: idx_49d19c8da30c442f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_49d19c8da30c442f ON espece_identifiee USING btree (date_precision_voc_fk);


--
-- TOC entry 2354 (class 1259 OID 67972)
-- Name: idx_49d19c8dcdd1f756; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_49d19c8dcdd1f756 ON espece_identifiee USING btree (sequence_assemblee_ext_fk);


--
-- TOC entry 2355 (class 1259 OID 67973)
-- Name: idx_49d19c8dfb5f790; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_49d19c8dfb5f790 ON espece_identifiee USING btree (critere_identification_voc_fk);


--
-- TOC entry 2311 (class 1259 OID 67974)
-- Name: idx_4e79cb8d40e7e0b3; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_4e79cb8d40e7e0b3 ON assigne USING btree (methode_motu_voc_fk);


--
-- TOC entry 2312 (class 1259 OID 67975)
-- Name: idx_4e79cb8d503b4409; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_4e79cb8d503b4409 ON assigne USING btree (motu_fk);


--
-- TOC entry 2313 (class 1259 OID 67976)
-- Name: idx_4e79cb8d5be90e48; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_4e79cb8d5be90e48 ON assigne USING btree (sequence_assemblee_fk);


--
-- TOC entry 2314 (class 1259 OID 67977)
-- Name: idx_4e79cb8dcdd1f756; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_4e79cb8dcdd1f756 ON assigne USING btree (sequence_assemblee_ext_fk);


--
-- TOC entry 2334 (class 1259 OID 67978)
-- Name: idx_55ae4a3d369ab36b; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_55ae4a3d369ab36b ON collecte USING btree (station_fk);


--
-- TOC entry 2335 (class 1259 OID 67979)
-- Name: idx_55ae4a3d50bb334e; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_55ae4a3d50bb334e ON collecte USING btree (leg_voc_fk);


--
-- TOC entry 2336 (class 1259 OID 67980)
-- Name: idx_55ae4a3da30c442f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_55ae4a3da30c442f ON collecte USING btree (date_precision_voc_fk);


--
-- TOC entry 2295 (class 1259 OID 67981)
-- Name: idx_5a6bd88a29b38195; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5a6bd88a29b38195 ON a_pour_sampling_method USING btree (sampling_method_voc_fk);


--
-- TOC entry 2296 (class 1259 OID 67982)
-- Name: idx_5a6bd88a662d9b98; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5a6bd88a662d9b98 ON a_pour_sampling_method USING btree (collecte_fk);


--
-- TOC entry 2441 (class 1259 OID 67983)
-- Name: idx_5b6b99362c5b04a7; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5b6b99362c5b04a7 ON pcr USING btree (primer_pcr_start_voc_fk);


--
-- TOC entry 2442 (class 1259 OID 67984)
-- Name: idx_5b6b99364b06319d; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5b6b99364b06319d ON pcr USING btree (adn_fk);


--
-- TOC entry 2443 (class 1259 OID 67985)
-- Name: idx_5b6b99366ccc2566; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5b6b99366ccc2566 ON pcr USING btree (specificite_voc_fk);


--
-- TOC entry 2444 (class 1259 OID 67986)
-- Name: idx_5b6b99368b4a1710; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5b6b99368b4a1710 ON pcr USING btree (qualite_pcr_voc_fk);


--
-- TOC entry 2445 (class 1259 OID 67987)
-- Name: idx_5b6b99369d3cdb05; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5b6b99369d3cdb05 ON pcr USING btree (gene_voc_fk);


--
-- TOC entry 2446 (class 1259 OID 67988)
-- Name: idx_5b6b9936a30c442f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5b6b9936a30c442f ON pcr USING btree (date_precision_voc_fk);


--
-- TOC entry 2447 (class 1259 OID 67989)
-- Name: idx_5b6b9936f1694267; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5b6b9936f1694267 ON pcr USING btree (primer_pcr_end_voc_fk);


--
-- TOC entry 2382 (class 1259 OID 67990)
-- Name: idx_5ee42fce4236d33e; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5ee42fce4236d33e ON individu USING btree (type_individu_voc_fk);


--
-- TOC entry 2383 (class 1259 OID 67991)
-- Name: idx_5ee42fce54dbbd4d; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_5ee42fce54dbbd4d ON individu USING btree (lot_materiel_fk);


--
-- TOC entry 2291 (class 1259 OID 67992)
-- Name: idx_60129a315fd841ac; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_60129a315fd841ac ON a_pour_fixateur USING btree (fixateur_voc_fk);


--
-- TOC entry 2292 (class 1259 OID 67993)
-- Name: idx_60129a31662d9b98; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_60129a31662d9b98 ON a_pour_fixateur USING btree (collecte_fk);


--
-- TOC entry 2408 (class 1259 OID 67994)
-- Name: idx_69c58aff54dbbd4d; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_69c58aff54dbbd4d ON lot_materiel_est_realise_par USING btree (lot_materiel_fk);


--
-- TOC entry 2409 (class 1259 OID 67995)
-- Name: idx_69c58affb53cd04c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_69c58affb53cd04c ON lot_materiel_est_realise_par USING btree (personne_fk);


--
-- TOC entry 2319 (class 1259 OID 67996)
-- Name: idx_7718edef41a72d48; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_7718edef41a72d48 ON boite USING btree (code_collection_voc_fk);


--
-- TOC entry 2320 (class 1259 OID 67997)
-- Name: idx_7718edef57552d30; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_7718edef57552d30 ON boite USING btree (type_boite_voc_fk);


--
-- TOC entry 2321 (class 1259 OID 67998)
-- Name: idx_7718edef9e7b0e1f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_7718edef9e7b0e1f ON boite USING btree (type_collection_voc_fk);


--
-- TOC entry 2421 (class 1259 OID 67999)
-- Name: idx_7d78636f40d80ecd; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_7d78636f40d80ecd ON lot_materiel_ext_est_realise_par USING btree (lot_materiel_ext_fk);


--
-- TOC entry 2422 (class 1259 OID 68000)
-- Name: idx_7d78636fb53cd04c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_7d78636fb53cd04c ON lot_materiel_ext_est_realise_par USING btree (personne_fk);


--
-- TOC entry 2393 (class 1259 OID 68001)
-- Name: idx_88295540b53cd04c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_88295540b53cd04c ON individu_lame_est_realise_par USING btree (personne_fk);


--
-- TOC entry 2394 (class 1259 OID 68002)
-- Name: idx_88295540d9c85992; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_88295540d9c85992 ON individu_lame_est_realise_par USING btree (individu_lame_fk);


--
-- TOC entry 2508 (class 1259 OID 68003)
-- Name: idx_8d0e8d6a821b1d3f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_8d0e8d6a821b1d3f ON sqc_ext_est_reference_dans USING btree (source_fk);


--
-- TOC entry 2509 (class 1259 OID 68004)
-- Name: idx_8d0e8d6acdd1f756; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_8d0e8d6acdd1f756 ON sqc_ext_est_reference_dans USING btree (sequence_assemblee_ext_fk);


--
-- TOC entry 2388 (class 1259 OID 68005)
-- Name: idx_8da827e22b644673; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_8da827e22b644673 ON individu_lame USING btree (boite_fk);


--
-- TOC entry 2389 (class 1259 OID 68006)
-- Name: idx_8da827e25f2c6176; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_8da827e25f2c6176 ON individu_lame USING btree (individu_fk);


--
-- TOC entry 2390 (class 1259 OID 68007)
-- Name: idx_8da827e2a30c442f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_8da827e2a30c442f ON individu_lame USING btree (date_precision_voc_fk);


--
-- TOC entry 2485 (class 1259 OID 68008)
-- Name: idx_9e9f85cf514d78e0; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9e9f85cf514d78e0 ON sequence_assemblee_ext USING btree (origine_sqc_ass_ext_voc_fk);


--
-- TOC entry 2486 (class 1259 OID 68009)
-- Name: idx_9e9f85cf662d9b98; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9e9f85cf662d9b98 ON sequence_assemblee_ext USING btree (collecte_fk);


--
-- TOC entry 2487 (class 1259 OID 68010)
-- Name: idx_9e9f85cf88085e0f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9e9f85cf88085e0f ON sequence_assemblee_ext USING btree (statut_sqc_ass_voc_fk);


--
-- TOC entry 2488 (class 1259 OID 68011)
-- Name: idx_9e9f85cf9d3cdb05; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9e9f85cf9d3cdb05 ON sequence_assemblee_ext USING btree (gene_voc_fk);


--
-- TOC entry 2489 (class 1259 OID 68012)
-- Name: idx_9e9f85cfa30c442f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9e9f85cfa30c442f ON sequence_assemblee_ext USING btree (date_precision_voc_fk);


--
-- TOC entry 2514 (class 1259 OID 68013)
-- Name: idx_9f39f8b143d4e2c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9f39f8b143d4e2c ON station USING btree (commune_fk);


--
-- TOC entry 2515 (class 1259 OID 68014)
-- Name: idx_9f39f8b14d50d031; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9f39f8b14d50d031 ON station USING btree (point_acces_voc_fk);


--
-- TOC entry 2516 (class 1259 OID 68015)
-- Name: idx_9f39f8b1b1c3431a; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9f39f8b1b1c3431a ON station USING btree (pays_fk);


--
-- TOC entry 2517 (class 1259 OID 68016)
-- Name: idx_9f39f8b1c23046ae; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9f39f8b1c23046ae ON station USING btree (habitat_type_voc_fk);


--
-- TOC entry 2518 (class 1259 OID 68017)
-- Name: idx_9f39f8b1e86dbd90; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_9f39f8b1e86dbd90 ON station USING btree (precision_lat_long_voc_fk);


--
-- TOC entry 2307 (class 1259 OID 68018)
-- Name: idx_b786c5214b06319d; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_b786c5214b06319d ON adn_est_realise_par USING btree (adn_fk);


--
-- TOC entry 2308 (class 1259 OID 68019)
-- Name: idx_b786c521b53cd04c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_b786c521b53cd04c ON adn_est_realise_par USING btree (personne_fk);


--
-- TOC entry 2401 (class 1259 OID 68020)
-- Name: idx_ba1841a52b644673; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_ba1841a52b644673 ON lot_materiel USING btree (boite_fk);


--
-- TOC entry 2402 (class 1259 OID 68021)
-- Name: idx_ba1841a5662d9b98; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_ba1841a5662d9b98 ON lot_materiel USING btree (collecte_fk);


--
-- TOC entry 2403 (class 1259 OID 68022)
-- Name: idx_ba1841a5a30c442f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_ba1841a5a30c442f ON lot_materiel USING btree (date_precision_voc_fk);


--
-- TOC entry 2404 (class 1259 OID 68023)
-- Name: idx_ba1841a5a897cc9e; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_ba1841a5a897cc9e ON lot_materiel USING btree (yeux_voc_fk);


--
-- TOC entry 2405 (class 1259 OID 68024)
-- Name: idx_ba1841a5b0b56b73; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_ba1841a5b0b56b73 ON lot_materiel USING btree (pigmentation_voc_fk);


--
-- TOC entry 2500 (class 1259 OID 68025)
-- Name: idx_ba97b9c45be90e48; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_ba97b9c45be90e48 ON sqc_est_publie_dans USING btree (sequence_assemblee_fk);


--
-- TOC entry 2501 (class 1259 OID 68026)
-- Name: idx_ba97b9c4821b1d3f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_ba97b9c4821b1d3f ON sqc_est_publie_dans USING btree (source_fk);


--
-- TOC entry 2358 (class 1259 OID 68027)
-- Name: idx_bd45639e5be90e48; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_bd45639e5be90e48 ON est_aligne_et_traite USING btree (sequence_assemblee_fk);


--
-- TOC entry 2359 (class 1259 OID 68028)
-- Name: idx_bd45639eefcfd332; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_bd45639eefcfd332 ON est_aligne_et_traite USING btree (chromatogramme_fk);


--
-- TOC entry 2287 (class 1259 OID 68029)
-- Name: idx_c0df0ce4662d9b98; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_c0df0ce4662d9b98 ON a_cibler USING btree (collecte_fk);


--
-- TOC entry 2288 (class 1259 OID 68030)
-- Name: idx_c0df0ce47b09e3bc; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_c0df0ce47b09e3bc ON a_cibler USING btree (referentiel_taxon_fk);


--
-- TOC entry 2425 (class 1259 OID 68031)
-- Name: idx_d2338bb240d80ecd; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_d2338bb240d80ecd ON lot_materiel_ext_est_reference_dans USING btree (lot_materiel_ext_fk);


--
-- TOC entry 2426 (class 1259 OID 68032)
-- Name: idx_d2338bb2821b1d3f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_d2338bb2821b1d3f ON lot_materiel_ext_est_reference_dans USING btree (source_fk);


--
-- TOC entry 2504 (class 1259 OID 68033)
-- Name: idx_dc41e25ab53cd04c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_dc41e25ab53cd04c ON sqc_ext_est_realise_par USING btree (personne_fk);


--
-- TOC entry 2505 (class 1259 OID 68034)
-- Name: idx_dc41e25acdd1f756; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_dc41e25acdd1f756 ON sqc_ext_est_realise_par USING btree (sequence_assemblee_ext_fk);


--
-- TOC entry 2341 (class 1259 OID 68035)
-- Name: idx_e2e2d1eeb1c3431a; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_e2e2d1eeb1c3431a ON commune USING btree (pays_fk);


--
-- TOC entry 2362 (class 1259 OID 68036)
-- Name: idx_ee2a88c9662d9b98; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_ee2a88c9662d9b98 ON est_effectue_par USING btree (collecte_fk);


--
-- TOC entry 2363 (class 1259 OID 68037)
-- Name: idx_ee2a88c9b53cd04c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_ee2a88c9b53cd04c ON est_effectue_par USING btree (personne_fk);


--
-- TOC entry 2414 (class 1259 OID 68038)
-- Name: idx_eefa43f3662d9b98; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_eefa43f3662d9b98 ON lot_materiel_ext USING btree (collecte_fk);


--
-- TOC entry 2415 (class 1259 OID 68039)
-- Name: idx_eefa43f382acdc4; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_eefa43f382acdc4 ON lot_materiel_ext USING btree (nb_individus_voc_fk);


--
-- TOC entry 2416 (class 1259 OID 68040)
-- Name: idx_eefa43f3a30c442f; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_eefa43f3a30c442f ON lot_materiel_ext USING btree (date_precision_voc_fk);


--
-- TOC entry 2417 (class 1259 OID 68041)
-- Name: idx_eefa43f3a897cc9e; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_eefa43f3a897cc9e ON lot_materiel_ext USING btree (yeux_voc_fk);


--
-- TOC entry 2418 (class 1259 OID 68042)
-- Name: idx_eefa43f3b0b56b73; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_eefa43f3b0b56b73 ON lot_materiel_ext USING btree (pigmentation_voc_fk);


--
-- TOC entry 2477 (class 1259 OID 68043)
-- Name: idx_f6971ba85be90e48; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_f6971ba85be90e48 ON sequence_assemblee_est_realise_par USING btree (sequence_assemblee_fk);


--
-- TOC entry 2478 (class 1259 OID 68044)
-- Name: idx_f6971ba8b53cd04c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_f6971ba8b53cd04c ON sequence_assemblee_est_realise_par USING btree (personne_fk);


--
-- TOC entry 2370 (class 1259 OID 68045)
-- Name: idx_f8fccf63b4ab6ba0; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_f8fccf63b4ab6ba0 ON est_identifie_par USING btree (espece_identifiee_fk);


--
-- TOC entry 2371 (class 1259 OID 68046)
-- Name: idx_f8fccf63b53cd04c; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_f8fccf63b53cd04c ON est_identifie_par USING btree (personne_fk);


--
-- TOC entry 2326 (class 1259 OID 68047)
-- Name: idx_fcb2dab7206fe5c0; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_fcb2dab7206fe5c0 ON chromatogramme USING btree (qualite_chromato_voc_fk);


--
-- TOC entry 2327 (class 1259 OID 68048)
-- Name: idx_fcb2dab7286bbca9; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_fcb2dab7286bbca9 ON chromatogramme USING btree (primer_chromato_voc_fk);


--
-- TOC entry 2328 (class 1259 OID 68049)
-- Name: idx_fcb2dab72b63d494; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_fcb2dab72b63d494 ON chromatogramme USING btree (pcr_fk);


--
-- TOC entry 2329 (class 1259 OID 68050)
-- Name: idx_fcb2dab7e8441376; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_fcb2dab7e8441376 ON chromatogramme USING btree (etablissement_fk);


--
-- TOC entry 2456 (class 1259 OID 68051)
-- Name: idx_fcec9efe8441376; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_fcec9efe8441376 ON personne USING btree (etablissement_fk);


--
-- TOC entry 2621 (class 2606 OID 68053)
-- Name: sqc_est_publie_dans ce_cle_etrangere; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sqc_est_publie_dans
    ADD CONSTRAINT ce_cle_etrangere FOREIGN KEY (source_fk) REFERENCES source(id) ON DELETE CASCADE;


--
-- TOC entry 2622 (class 2606 OID 68058)
-- Name: sqc_est_publie_dans ce_cle_etrangere1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sqc_est_publie_dans
    ADD CONSTRAINT ce_cle_etrangere1 FOREIGN KEY (sequence_assemblee_fk) REFERENCES sequence_assemblee(id) ON DELETE CASCADE;


--
-- TOC entry 2571 (class 2606 OID 68063)
-- Name: est_identifie_par ce_cle_etrangere10; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY est_identifie_par
    ADD CONSTRAINT ce_cle_etrangere10 FOREIGN KEY (personne_fk) REFERENCES personne(id);


--
-- TOC entry 2569 (class 2606 OID 68068)
-- Name: est_finance_par ce_cle_etrangere100; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY est_finance_par
    ADD CONSTRAINT ce_cle_etrangere100 FOREIGN KEY (programme_fk) REFERENCES programme(id);


--
-- TOC entry 2570 (class 2606 OID 68073)
-- Name: est_finance_par ce_cle_etrangere101; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY est_finance_par
    ADD CONSTRAINT ce_cle_etrangere101 FOREIGN KEY (collecte_fk) REFERENCES collecte(id) ON DELETE CASCADE;


--
-- TOC entry 2575 (class 2606 OID 68078)
-- Name: individu_lame ce_cle_etrangere102; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY individu_lame
    ADD CONSTRAINT ce_cle_etrangere102 FOREIGN KEY (date_precision_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2576 (class 2606 OID 68083)
-- Name: individu_lame ce_cle_etrangere103; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY individu_lame
    ADD CONSTRAINT ce_cle_etrangere103 FOREIGN KEY (boite_fk) REFERENCES boite(id);


--
-- TOC entry 2577 (class 2606 OID 68088)
-- Name: individu_lame ce_cle_etrangere104; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY individu_lame
    ADD CONSTRAINT ce_cle_etrangere104 FOREIGN KEY (individu_fk) REFERENCES individu(id);


--
-- TOC entry 2540 (class 2606 OID 68093)
-- Name: assigne ce_cle_etrangere11; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY assigne
    ADD CONSTRAINT ce_cle_etrangere11 FOREIGN KEY (sequence_assemblee_ext_fk) REFERENCES sequence_assemblee_ext(id);


--
-- TOC entry 2541 (class 2606 OID 68098)
-- Name: assigne ce_cle_etrangere12; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY assigne
    ADD CONSTRAINT ce_cle_etrangere12 FOREIGN KEY (methode_motu_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2542 (class 2606 OID 68103)
-- Name: assigne ce_cle_etrangere13; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY assigne
    ADD CONSTRAINT ce_cle_etrangere13 FOREIGN KEY (sequence_assemblee_fk) REFERENCES sequence_assemblee(id);


--
-- TOC entry 2543 (class 2606 OID 68108)
-- Name: assigne ce_cle_etrangere14; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY assigne
    ADD CONSTRAINT ce_cle_etrangere14 FOREIGN KEY (motu_fk) REFERENCES motu(id) ON DELETE CASCADE;


--
-- TOC entry 2555 (class 2606 OID 68113)
-- Name: composition_lot_materiel ce_cle_etrangere15; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY composition_lot_materiel
    ADD CONSTRAINT ce_cle_etrangere15 FOREIGN KEY (type_individu_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2556 (class 2606 OID 68118)
-- Name: composition_lot_materiel ce_cle_etrangere16; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY composition_lot_materiel
    ADD CONSTRAINT ce_cle_etrangere16 FOREIGN KEY (lot_materiel_fk) REFERENCES lot_materiel(id) ON DELETE CASCADE;


--
-- TOC entry 2527 (class 2606 OID 68123)
-- Name: a_cibler ce_cle_etrangere17; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY a_cibler
    ADD CONSTRAINT ce_cle_etrangere17 FOREIGN KEY (collecte_fk) REFERENCES collecte(id) ON DELETE CASCADE;


--
-- TOC entry 2528 (class 2606 OID 68128)
-- Name: a_cibler ce_cle_etrangere18; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY a_cibler
    ADD CONSTRAINT ce_cle_etrangere18 FOREIGN KEY (referentiel_taxon_fk) REFERENCES referentiel_taxon(id);


--
-- TOC entry 2612 (class 2606 OID 68133)
-- Name: sequence_assemblee_est_realise_par ce_cle_etrangere19; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sequence_assemblee_est_realise_par
    ADD CONSTRAINT ce_cle_etrangere19 FOREIGN KEY (sequence_assemblee_fk) REFERENCES sequence_assemblee(id) ON DELETE CASCADE;


--
-- TOC entry 2614 (class 2606 OID 68138)
-- Name: sequence_assemblee_ext ce_cle_etrangere2; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sequence_assemblee_ext
    ADD CONSTRAINT ce_cle_etrangere2 FOREIGN KEY (gene_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2613 (class 2606 OID 68143)
-- Name: sequence_assemblee_est_realise_par ce_cle_etrangere20; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sequence_assemblee_est_realise_par
    ADD CONSTRAINT ce_cle_etrangere20 FOREIGN KEY (personne_fk) REFERENCES personne(id);


--
-- TOC entry 2607 (class 2606 OID 68148)
-- Name: pcr_est_realise_par ce_cle_etrangere21; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY pcr_est_realise_par
    ADD CONSTRAINT ce_cle_etrangere21 FOREIGN KEY (pcr_fk) REFERENCES pcr(id) ON DELETE CASCADE;


--
-- TOC entry 2608 (class 2606 OID 68153)
-- Name: pcr_est_realise_par ce_cle_etrangere22; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY pcr_est_realise_par
    ADD CONSTRAINT ce_cle_etrangere22 FOREIGN KEY (personne_fk) REFERENCES personne(id);


--
-- TOC entry 2538 (class 2606 OID 68158)
-- Name: adn_est_realise_par ce_cle_etrangere23; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY adn_est_realise_par
    ADD CONSTRAINT ce_cle_etrangere23 FOREIGN KEY (adn_fk) REFERENCES adn(id) ON DELETE CASCADE;


--
-- TOC entry 2539 (class 2606 OID 68163)
-- Name: adn_est_realise_par ce_cle_etrangere24; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY adn_est_realise_par
    ADD CONSTRAINT ce_cle_etrangere24 FOREIGN KEY (personne_fk) REFERENCES personne(id);


--
-- TOC entry 2578 (class 2606 OID 68168)
-- Name: individu_lame_est_realise_par ce_cle_etrangere25; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY individu_lame_est_realise_par
    ADD CONSTRAINT ce_cle_etrangere25 FOREIGN KEY (individu_lame_fk) REFERENCES individu_lame(id) ON DELETE CASCADE;


--
-- TOC entry 2579 (class 2606 OID 68173)
-- Name: individu_lame_est_realise_par ce_cle_etrangere26; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY individu_lame_est_realise_par
    ADD CONSTRAINT ce_cle_etrangere26 FOREIGN KEY (personne_fk) REFERENCES personne(id);


--
-- TOC entry 2587 (class 2606 OID 68178)
-- Name: lot_materiel_est_realise_par ce_cle_etrangere27; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel_est_realise_par
    ADD CONSTRAINT ce_cle_etrangere27 FOREIGN KEY (lot_materiel_fk) REFERENCES lot_materiel(id) ON DELETE CASCADE;


--
-- TOC entry 2588 (class 2606 OID 68183)
-- Name: lot_materiel_est_realise_par ce_cle_etrangere28; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel_est_realise_par
    ADD CONSTRAINT ce_cle_etrangere28 FOREIGN KEY (personne_fk) REFERENCES personne(id);


--
-- TOC entry 2619 (class 2606 OID 68188)
-- Name: source_a_ete_integre_par ce_cle_etrangere29; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY source_a_ete_integre_par
    ADD CONSTRAINT ce_cle_etrangere29 FOREIGN KEY (source_fk) REFERENCES source(id) ON DELETE CASCADE;


--
-- TOC entry 2615 (class 2606 OID 68193)
-- Name: sequence_assemblee_ext ce_cle_etrangere3; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sequence_assemblee_ext
    ADD CONSTRAINT ce_cle_etrangere3 FOREIGN KEY (date_precision_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2620 (class 2606 OID 68198)
-- Name: source_a_ete_integre_par ce_cle_etrangere30; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY source_a_ete_integre_par
    ADD CONSTRAINT ce_cle_etrangere30 FOREIGN KEY (personne_fk) REFERENCES personne(id);


--
-- TOC entry 2623 (class 2606 OID 68203)
-- Name: sqc_ext_est_realise_par ce_cle_etrangere31; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sqc_ext_est_realise_par
    ADD CONSTRAINT ce_cle_etrangere31 FOREIGN KEY (sequence_assemblee_ext_fk) REFERENCES sequence_assemblee_ext(id) ON DELETE CASCADE;


--
-- TOC entry 2624 (class 2606 OID 68208)
-- Name: sqc_ext_est_realise_par ce_cle_etrangere32; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sqc_ext_est_realise_par
    ADD CONSTRAINT ce_cle_etrangere32 FOREIGN KEY (personne_fk) REFERENCES personne(id);


--
-- TOC entry 2598 (class 2606 OID 68213)
-- Name: motu_est_genere_par ce_cle_etrangere33; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY motu_est_genere_par
    ADD CONSTRAINT ce_cle_etrangere33 FOREIGN KEY (motu_fk) REFERENCES motu(id) ON DELETE CASCADE;


--
-- TOC entry 2599 (class 2606 OID 68218)
-- Name: motu_est_genere_par ce_cle_etrangere34; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY motu_est_genere_par
    ADD CONSTRAINT ce_cle_etrangere34 FOREIGN KEY (personne_fk) REFERENCES personne(id);


--
-- TOC entry 2610 (class 2606 OID 68223)
-- Name: sequence_assemblee ce_cle_etrangere35; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sequence_assemblee
    ADD CONSTRAINT ce_cle_etrangere35 FOREIGN KEY (date_precision_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2611 (class 2606 OID 68228)
-- Name: sequence_assemblee ce_cle_etrangere36; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sequence_assemblee
    ADD CONSTRAINT ce_cle_etrangere36 FOREIGN KEY (statut_sqc_ass_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2565 (class 2606 OID 68233)
-- Name: est_aligne_et_traite ce_cle_etrangere37; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY est_aligne_et_traite
    ADD CONSTRAINT ce_cle_etrangere37 FOREIGN KEY (chromatogramme_fk) REFERENCES chromatogramme(id);


--
-- TOC entry 2566 (class 2606 OID 68238)
-- Name: est_aligne_et_traite ce_cle_etrangere38; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY est_aligne_et_traite
    ADD CONSTRAINT ce_cle_etrangere38 FOREIGN KEY (sequence_assemblee_fk) REFERENCES sequence_assemblee(id) ON DELETE CASCADE;


--
-- TOC entry 2554 (class 2606 OID 68243)
-- Name: commune ce_cle_etrangere39; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY commune
    ADD CONSTRAINT ce_cle_etrangere39 FOREIGN KEY (pays_fk) REFERENCES pays(id);


--
-- TOC entry 2616 (class 2606 OID 68248)
-- Name: sequence_assemblee_ext ce_cle_etrangere4; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sequence_assemblee_ext
    ADD CONSTRAINT ce_cle_etrangere4 FOREIGN KEY (origine_sqc_ass_ext_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2531 (class 2606 OID 68253)
-- Name: a_pour_sampling_method ce_cle_etrangere40; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY a_pour_sampling_method
    ADD CONSTRAINT ce_cle_etrangere40 FOREIGN KEY (sampling_method_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2532 (class 2606 OID 68258)
-- Name: a_pour_sampling_method ce_cle_etrangere41; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY a_pour_sampling_method
    ADD CONSTRAINT ce_cle_etrangere41 FOREIGN KEY (collecte_fk) REFERENCES collecte(id) ON DELETE CASCADE;


--
-- TOC entry 2529 (class 2606 OID 68263)
-- Name: a_pour_fixateur ce_cle_etrangere42; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY a_pour_fixateur
    ADD CONSTRAINT ce_cle_etrangere42 FOREIGN KEY (fixateur_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2530 (class 2606 OID 68268)
-- Name: a_pour_fixateur ce_cle_etrangere43; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY a_pour_fixateur
    ADD CONSTRAINT ce_cle_etrangere43 FOREIGN KEY (collecte_fk) REFERENCES collecte(id) ON DELETE CASCADE;


--
-- TOC entry 2580 (class 2606 OID 68273)
-- Name: lot_est_publie_dans ce_cle_etrangere44; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_est_publie_dans
    ADD CONSTRAINT ce_cle_etrangere44 FOREIGN KEY (lot_materiel_fk) REFERENCES lot_materiel(id) ON DELETE CASCADE;


--
-- TOC entry 2581 (class 2606 OID 68278)
-- Name: lot_est_publie_dans ce_cle_etrangere45; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_est_publie_dans
    ADD CONSTRAINT ce_cle_etrangere45 FOREIGN KEY (source_fk) REFERENCES source(id) ON DELETE CASCADE;


--
-- TOC entry 2589 (class 2606 OID 68283)
-- Name: lot_materiel_ext ce_cle_etrangere46; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel_ext
    ADD CONSTRAINT ce_cle_etrangere46 FOREIGN KEY (collecte_fk) REFERENCES collecte(id);


--
-- TOC entry 2590 (class 2606 OID 68288)
-- Name: lot_materiel_ext ce_cle_etrangere47; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel_ext
    ADD CONSTRAINT ce_cle_etrangere47 FOREIGN KEY (date_precision_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2591 (class 2606 OID 68293)
-- Name: lot_materiel_ext ce_cle_etrangere48; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel_ext
    ADD CONSTRAINT ce_cle_etrangere48 FOREIGN KEY (nb_individus_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2592 (class 2606 OID 68298)
-- Name: lot_materiel_ext ce_cle_etrangere49; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel_ext
    ADD CONSTRAINT ce_cle_etrangere49 FOREIGN KEY (pigmentation_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2617 (class 2606 OID 68303)
-- Name: sequence_assemblee_ext ce_cle_etrangere5; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sequence_assemblee_ext
    ADD CONSTRAINT ce_cle_etrangere5 FOREIGN KEY (collecte_fk) REFERENCES collecte(id);


--
-- TOC entry 2593 (class 2606 OID 68308)
-- Name: lot_materiel_ext ce_cle_etrangere50; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel_ext
    ADD CONSTRAINT ce_cle_etrangere50 FOREIGN KEY (yeux_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2594 (class 2606 OID 68313)
-- Name: lot_materiel_ext_est_realise_par ce_cle_etrangere51; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel_ext_est_realise_par
    ADD CONSTRAINT ce_cle_etrangere51 FOREIGN KEY (personne_fk) REFERENCES personne(id);


--
-- TOC entry 2595 (class 2606 OID 68318)
-- Name: lot_materiel_ext_est_realise_par ce_cle_etrangere52; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel_ext_est_realise_par
    ADD CONSTRAINT ce_cle_etrangere52 FOREIGN KEY (lot_materiel_ext_fk) REFERENCES lot_materiel_ext(id) ON DELETE CASCADE;


--
-- TOC entry 2596 (class 2606 OID 68323)
-- Name: lot_materiel_ext_est_reference_dans ce_cle_etrangere53; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel_ext_est_reference_dans
    ADD CONSTRAINT ce_cle_etrangere53 FOREIGN KEY (lot_materiel_ext_fk) REFERENCES lot_materiel_ext(id) ON DELETE CASCADE;


--
-- TOC entry 2597 (class 2606 OID 68328)
-- Name: lot_materiel_ext_est_reference_dans ce_cle_etrangere54; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel_ext_est_reference_dans
    ADD CONSTRAINT ce_cle_etrangere54 FOREIGN KEY (source_fk) REFERENCES source(id) ON DELETE CASCADE;


--
-- TOC entry 2627 (class 2606 OID 68333)
-- Name: station ce_cle_etrangere55; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY station
    ADD CONSTRAINT ce_cle_etrangere55 FOREIGN KEY (commune_fk) REFERENCES commune(id);


--
-- TOC entry 2628 (class 2606 OID 68338)
-- Name: station ce_cle_etrangere56; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY station
    ADD CONSTRAINT ce_cle_etrangere56 FOREIGN KEY (pays_fk) REFERENCES pays(id);


--
-- TOC entry 2629 (class 2606 OID 68343)
-- Name: station ce_cle_etrangere57; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY station
    ADD CONSTRAINT ce_cle_etrangere57 FOREIGN KEY (point_acces_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2630 (class 2606 OID 68348)
-- Name: station ce_cle_etrangere58; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY station
    ADD CONSTRAINT ce_cle_etrangere58 FOREIGN KEY (habitat_type_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2631 (class 2606 OID 68353)
-- Name: station ce_cle_etrangere59; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY station
    ADD CONSTRAINT ce_cle_etrangere59 FOREIGN KEY (precision_lat_long_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2618 (class 2606 OID 68358)
-- Name: sequence_assemblee_ext ce_cle_etrangere6; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sequence_assemblee_ext
    ADD CONSTRAINT ce_cle_etrangere6 FOREIGN KEY (statut_sqc_ass_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2551 (class 2606 OID 68363)
-- Name: collecte ce_cle_etrangere60; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY collecte
    ADD CONSTRAINT ce_cle_etrangere60 FOREIGN KEY (date_precision_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2552 (class 2606 OID 68368)
-- Name: collecte ce_cle_etrangere61; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY collecte
    ADD CONSTRAINT ce_cle_etrangere61 FOREIGN KEY (leg_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2553 (class 2606 OID 68373)
-- Name: collecte ce_cle_etrangere62; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY collecte
    ADD CONSTRAINT ce_cle_etrangere62 FOREIGN KEY (station_fk) REFERENCES station(id);


--
-- TOC entry 2609 (class 2606 OID 68378)
-- Name: personne ce_cle_etrangere63; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY personne
    ADD CONSTRAINT ce_cle_etrangere63 FOREIGN KEY (etablissement_fk) REFERENCES etablissement(id);


--
-- TOC entry 2567 (class 2606 OID 68383)
-- Name: est_effectue_par ce_cle_etrangere64; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY est_effectue_par
    ADD CONSTRAINT ce_cle_etrangere64 FOREIGN KEY (personne_fk) REFERENCES personne(id);


--
-- TOC entry 2568 (class 2606 OID 68388)
-- Name: est_effectue_par ce_cle_etrangere65; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY est_effectue_par
    ADD CONSTRAINT ce_cle_etrangere65 FOREIGN KEY (collecte_fk) REFERENCES collecte(id) ON DELETE CASCADE;


--
-- TOC entry 2544 (class 2606 OID 68393)
-- Name: boite ce_cle_etrangere66; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY boite
    ADD CONSTRAINT ce_cle_etrangere66 FOREIGN KEY (type_collection_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2545 (class 2606 OID 68398)
-- Name: boite ce_cle_etrangere67; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY boite
    ADD CONSTRAINT ce_cle_etrangere67 FOREIGN KEY (code_collection_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2546 (class 2606 OID 68403)
-- Name: boite ce_cle_etrangere68; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY boite
    ADD CONSTRAINT ce_cle_etrangere68 FOREIGN KEY (type_boite_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2582 (class 2606 OID 68408)
-- Name: lot_materiel ce_cle_etrangere69; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel
    ADD CONSTRAINT ce_cle_etrangere69 FOREIGN KEY (date_precision_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2625 (class 2606 OID 68413)
-- Name: sqc_ext_est_reference_dans ce_cle_etrangere7; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sqc_ext_est_reference_dans
    ADD CONSTRAINT ce_cle_etrangere7 FOREIGN KEY (source_fk) REFERENCES source(id) ON DELETE CASCADE;


--
-- TOC entry 2583 (class 2606 OID 68418)
-- Name: lot_materiel ce_cle_etrangere70; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel
    ADD CONSTRAINT ce_cle_etrangere70 FOREIGN KEY (pigmentation_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2584 (class 2606 OID 68423)
-- Name: lot_materiel ce_cle_etrangere71; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel
    ADD CONSTRAINT ce_cle_etrangere71 FOREIGN KEY (yeux_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2585 (class 2606 OID 68428)
-- Name: lot_materiel ce_cle_etrangere72; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel
    ADD CONSTRAINT ce_cle_etrangere72 FOREIGN KEY (collecte_fk) REFERENCES collecte(id);


--
-- TOC entry 2586 (class 2606 OID 68433)
-- Name: lot_materiel ce_cle_etrangere73; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lot_materiel
    ADD CONSTRAINT ce_cle_etrangere73 FOREIGN KEY (boite_fk) REFERENCES boite(id);


--
-- TOC entry 2557 (class 2606 OID 68438)
-- Name: espece_identifiee ce_cle_etrangere74; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY espece_identifiee
    ADD CONSTRAINT ce_cle_etrangere74 FOREIGN KEY (critere_identification_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2558 (class 2606 OID 68443)
-- Name: espece_identifiee ce_cle_etrangere75; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY espece_identifiee
    ADD CONSTRAINT ce_cle_etrangere75 FOREIGN KEY (date_precision_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2559 (class 2606 OID 68448)
-- Name: espece_identifiee ce_cle_etrangere76; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY espece_identifiee
    ADD CONSTRAINT ce_cle_etrangere76 FOREIGN KEY (sequence_assemblee_ext_fk) REFERENCES sequence_assemblee_ext(id) ON DELETE CASCADE;


--
-- TOC entry 2560 (class 2606 OID 68453)
-- Name: espece_identifiee ce_cle_etrangere77; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY espece_identifiee
    ADD CONSTRAINT ce_cle_etrangere77 FOREIGN KEY (lot_materiel_ext_fk) REFERENCES lot_materiel_ext(id) ON DELETE CASCADE;


--
-- TOC entry 2561 (class 2606 OID 68458)
-- Name: espece_identifiee ce_cle_etrangere78; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY espece_identifiee
    ADD CONSTRAINT ce_cle_etrangere78 FOREIGN KEY (lot_materiel_fk) REFERENCES lot_materiel(id) ON DELETE CASCADE;


--
-- TOC entry 2562 (class 2606 OID 68463)
-- Name: espece_identifiee ce_cle_etrangere79; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY espece_identifiee
    ADD CONSTRAINT ce_cle_etrangere79 FOREIGN KEY (referentiel_taxon_fk) REFERENCES referentiel_taxon(id);


--
-- TOC entry 2626 (class 2606 OID 68468)
-- Name: sqc_ext_est_reference_dans ce_cle_etrangere8; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sqc_ext_est_reference_dans
    ADD CONSTRAINT ce_cle_etrangere8 FOREIGN KEY (sequence_assemblee_ext_fk) REFERENCES sequence_assemblee_ext(id) ON DELETE CASCADE;


--
-- TOC entry 2563 (class 2606 OID 68473)
-- Name: espece_identifiee ce_cle_etrangere80; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY espece_identifiee
    ADD CONSTRAINT ce_cle_etrangere80 FOREIGN KEY (individu_fk) REFERENCES individu(id) ON DELETE CASCADE;


--
-- TOC entry 2564 (class 2606 OID 68478)
-- Name: espece_identifiee ce_cle_etrangere81; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY espece_identifiee
    ADD CONSTRAINT ce_cle_etrangere81 FOREIGN KEY (sequence_assemblee_fk) REFERENCES sequence_assemblee(id) ON DELETE CASCADE;


--
-- TOC entry 2573 (class 2606 OID 68483)
-- Name: individu ce_cle_etrangere82; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY individu
    ADD CONSTRAINT ce_cle_etrangere82 FOREIGN KEY (type_individu_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2574 (class 2606 OID 68488)
-- Name: individu ce_cle_etrangere83; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY individu
    ADD CONSTRAINT ce_cle_etrangere83 FOREIGN KEY (lot_materiel_fk) REFERENCES lot_materiel(id);


--
-- TOC entry 2533 (class 2606 OID 68493)
-- Name: adn ce_cle_etrangere84; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY adn
    ADD CONSTRAINT ce_cle_etrangere84 FOREIGN KEY (date_precision_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2534 (class 2606 OID 68498)
-- Name: adn ce_cle_etrangere85; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY adn
    ADD CONSTRAINT ce_cle_etrangere85 FOREIGN KEY (methode_extraction_adn_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2535 (class 2606 OID 68503)
-- Name: adn ce_cle_etrangere86; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY adn
    ADD CONSTRAINT ce_cle_etrangere86 FOREIGN KEY (individu_fk) REFERENCES individu(id);


--
-- TOC entry 2536 (class 2606 OID 68508)
-- Name: adn ce_cle_etrangere87; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY adn
    ADD CONSTRAINT ce_cle_etrangere87 FOREIGN KEY (qualite_adn_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2537 (class 2606 OID 68513)
-- Name: adn ce_cle_etrangere88; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY adn
    ADD CONSTRAINT ce_cle_etrangere88 FOREIGN KEY (boite_fk) REFERENCES boite(id);


--
-- TOC entry 2600 (class 2606 OID 68518)
-- Name: pcr ce_cle_etrangere89; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY pcr
    ADD CONSTRAINT ce_cle_etrangere89 FOREIGN KEY (gene_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2572 (class 2606 OID 68523)
-- Name: est_identifie_par ce_cle_etrangere9; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY est_identifie_par
    ADD CONSTRAINT ce_cle_etrangere9 FOREIGN KEY (espece_identifiee_fk) REFERENCES espece_identifiee(id) ON DELETE CASCADE;


--
-- TOC entry 2601 (class 2606 OID 68528)
-- Name: pcr ce_cle_etrangere90; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY pcr
    ADD CONSTRAINT ce_cle_etrangere90 FOREIGN KEY (qualite_pcr_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2602 (class 2606 OID 68533)
-- Name: pcr ce_cle_etrangere91; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY pcr
    ADD CONSTRAINT ce_cle_etrangere91 FOREIGN KEY (specificite_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2603 (class 2606 OID 68538)
-- Name: pcr ce_cle_etrangere92; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY pcr
    ADD CONSTRAINT ce_cle_etrangere92 FOREIGN KEY (primer_pcr_start_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2604 (class 2606 OID 68543)
-- Name: pcr ce_cle_etrangere93; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY pcr
    ADD CONSTRAINT ce_cle_etrangere93 FOREIGN KEY (primer_pcr_end_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2605 (class 2606 OID 68548)
-- Name: pcr ce_cle_etrangere94; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY pcr
    ADD CONSTRAINT ce_cle_etrangere94 FOREIGN KEY (date_precision_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2606 (class 2606 OID 68553)
-- Name: pcr ce_cle_etrangere95; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY pcr
    ADD CONSTRAINT ce_cle_etrangere95 FOREIGN KEY (adn_fk) REFERENCES adn(id);


--
-- TOC entry 2547 (class 2606 OID 68558)
-- Name: chromatogramme ce_cle_etrangere96; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY chromatogramme
    ADD CONSTRAINT ce_cle_etrangere96 FOREIGN KEY (primer_chromato_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2548 (class 2606 OID 68563)
-- Name: chromatogramme ce_cle_etrangere97; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY chromatogramme
    ADD CONSTRAINT ce_cle_etrangere97 FOREIGN KEY (qualite_chromato_voc_fk) REFERENCES voc(id);


--
-- TOC entry 2549 (class 2606 OID 68568)
-- Name: chromatogramme ce_cle_etrangere98; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY chromatogramme
    ADD CONSTRAINT ce_cle_etrangere98 FOREIGN KEY (etablissement_fk) REFERENCES etablissement(id);


--
-- TOC entry 2550 (class 2606 OID 68573)
-- Name: chromatogramme ce_cle_etrangere99; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY chromatogramme
    ADD CONSTRAINT ce_cle_etrangere99 FOREIGN KEY (pcr_fk) REFERENCES pcr(id);


--
-- TOC entry 2747 (class 0 OID 0)
-- Dependencies: 9
-- Name: public; Type: ACL; Schema: -; Owner: -
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2019-02-08 15:05:21

--
-- PostgreSQL database dump complete
--

