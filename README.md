GOTIT : Gene, Occurence and Taxa in Integrative Taxonomy
=====================

A Symfony project updated on 13/03/2019 : Publish Version 1.1.1

**Authors :** Florian MALARD (1), Philippe GRISON (2), Louis DUCHEMIN (1), Lara KONECNY-DUPRE (1), Tristan LEFEBURE (1), Nathanaëlle Saclier (1), David Eme (3), Chloé MARTIN (2), Cécile CALLOU (2), Christope DOUADY (1)

(1) LEHNA : UMR CNRS 5023 Ecologie des Hydrosystèmes Naturels et Anthropisés, Université Lyon 1, ENTPE, CNRS, Université Lyon

(2) BBEES : Unité Bases de données sur la Biodiversité, Écologie, Environnement et Sociétés Muséum national d'Histoire naturelle, CNRS ; CP55, 57 rue Cuvier 75005 Paris, France

(3) New Zealand Inst. for Advanced Studies, Inst. of Natural and Mathematical Sciences, Massey Univ., Auckland, New Zealand


Licence : GPL-v3

## Project 
It includes a postgreSQL database and web interface for optimizing the input, management, and sharing of species occurrence data, metadata and vouchers produced on a day-by-day basis by biodiversity laboratories involved in the delimitation, inventory and distribution of species.

The tool is particularly suitable for designing and monitoring biodiversity projects that employ an integrative taxonomic approach combining morphology-based and DNA-based species occurrence data to explain biodiversity patterns.

In addition to managing multi-criteria species occurrence data, GOTIT offers a number of functionalities that are pivotal in optimizing biodiversity research within laboratories.
First, it provides the necessary traceability for recovering the full set of methods and biological material linked to any species taxa occurrence data produced by the hosting laboratory.
Second, the database accommodates species occurrence and DNA sequence data that are not produced by the hosting laboratory, thereby enabling any user working on a particular set of taxa to dispose of all available data concerning those taxa into a single database.

Third, access to the database can be granted to any users, including those outside the hosting laboratory.
This promotes information sharing among laboratories while managing the privileges allocated to each user.
At last, GOTIT is implemented and distributed following public license practices, so that the tool can be adapted by advanced developers to fulfill a laboratories specific requirements.


# Documentation

- [admin GOTIT installation](https://github.com/GOTIT-DEV/GOTIT/blob/v1.1.1/install/1.1/doc/GOTIT_Install.pdf)
- [user web interface help](https://github.com/GOTIT-DEV/GOTIT/blob/v1.1.1/install/1.1/doc/S3_GOTIT_Help.pdf)
- database models [MCD](https://github.com/GOTIT-DEV/GOTIT/blob/v1.1.1/install/1.1/doc/database/S3_Gotitdb_conceptual_model_en.jpg)/[MRD](https://github.com/GOTIT-DEV/GOTIT/blob/v1.1.1/install/1.1/doc/database/S1_Gotitdb_logical_model_en.jpg) and [tables/fields description](https://github.com/GOTIT-DEV/GOTIT/blob/v1.1.1/install/1.1/doc/database/S5_Gotitdb_tables_fields.ods)

# Files :

- [release v1.1.1](https://github.com/GOTIT-DEV/GOTIT/archive/v1.1.1.zip)
- [templates of csv files](https://github.com/GOTIT-DEV/GOTIT/blob/v1.1.1/install/1.1/template.zip)
- [vocabulary.csv](https://github.com/GOTIT-DEV/GOTIT/blob/v1.1.1/install/1.1/vocabulary_gotit1-1.csv) with all scientific and other vocabulary 

# GOTIT History :

- The GOTIT project was first launched in 2015 between BBEES and LEHNA
- The first conceptual and logical data model of the relational data base were established in May 2017 with further refinements thenafter
- The postgreSQL database was first operational in July 2017 with further refinements thenafter
- Benchmark data were migrated to the database in Oct 2017 with further migrations thenafter
- The specifications for the web interface including not less than 30 use cases was achieved in June 2018.
- The first alpha version of the web interface was produced in August 2018
- Version 1.0 was produced in October 2018
- Version 1.1 with full documentation was released on February 2019
- A demo version of GOTIT was made available at [https://gotit.cnrs.fr](https://gotit.cnrs.fr) on March 2019
- We anticipate to release an uggraded version of GOTIT current 2020/2021 [see the ROADMAP section of the wiki](https://github.com/GOTIT-DEV/GOTIT/wiki/ROADMAP)

----

Notes : BBEES laboratory will not support any development of GOTIT in response to sollicitations other than those of LEHNA laboratory.
