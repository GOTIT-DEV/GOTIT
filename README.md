# GOTIT : Gene, Occurence & Taxa in Integrative Taxonomy - GOTIT 3.0 -Symf5.4LTS / PHP8

A postgreSQL database and web interface for optimizing the input, management, and sharing of species occurrence data, metadata and vouchers produced on a day-by-day basis by biodiversity laboratories involved in the delimitation, inventory and distribution of species.

The tool is particularly suitable for designing and monitoring biodiversity projects that employ an integrative taxonomic approach combining morphology-based and DNA-based species occurrence data to explain biodiversity patterns.

In addition to managing multi-criteria species occurrence data, GOTIT offers a number of functionalities that are pivotal in optimizing biodiversity research within laboratories. First, it provides the necessary traceability for recovering the full set of methods and biological material linked to any species taxa occurrence data produced by the hosting laboratory. Second, the database accommodates species occurrence and DNA sequence data that are not produced by the hosting laboratory, thereby enabling any user working on a particular set of taxa to dispose of all available data concerning those taxa into a single database.

Third, access to the database can be granted to any users, including those outside the hosting laboratory. This promotes information sharing among laboratories while managing the privileges allocated to each user. At last, GOTIT is implemented and distributed following public license practices, so that the tool can be adapted by advanced developers to fulfill a laboratories specific requirements.



# Documentation

- [documentation](./docs/)
- [CSV templates for imports](./assets/imports)

# Development setup

**Requirements :**

- PHP8.1
- [Yarn v1](https://classic.yarnpkg.com/)
- [Composer](https://getcomposer.org/)
- [Symfony CLI](https://symfony.com/download)

**Install dependencies**
 1. `cd` to the root of the project
 2. Run `composer install` to install PHP dependencies
 3. Run `yarn` to install JS dependencies

**[Optional] Setup local development database**

A docker image to run the Gotit PostgreSQL database with mock data is publicly available on docker hub : [`lsdch/gotit-db:2.0`](https://hub.docker.com/repository/docker/lsdch/gotit-db).

**Setup environment**

This repository includes a template `.env` file at its root.
1. Copy that file to a new `.env.local` file in the root directory. It must not be committed and is already listed in `.gitignore`.
2. Change `APP_DEBUG=0` to `APP_DEBUG=1`
3. Change `APP_SECRET` to a long random string of your choice
4. Setup `DATABASE_URL` according to the template string provided .

**Start development servers**
- Run `symfony serve`
- Run `yarn dev-server` to start the assets server

Good to go !

---

Authors : Florian MALARD (1), Philippe GRISON (2), Louis DUCHEMIN (1), Maud Ferrer (1), Lara KONECNY-DUPRE (1), Tristan LEFEBURE (1), Nathanaëlle Saclier (1), David Eme (3), Chloé MARTIN (2), Cécile CALLOU (2), Christophe DOUADY (1)

(1) LEHNA : UMR CNRS 5023 Ecologie des Hydrosystèmes Naturels et Anthropisés, Université Lyon 1, ENTPE, CNRS, Université Lyon

(2) BBEES : Unité Bases de données sur la Biodiversité, Écologie, Environnement et Sociétés Muséum national d'Histoire naturelle, CNRS ; CP55, 57 rue Cuvier 75005 Paris, France

(3) New Zealand Inst. for Advanced Studies, Inst. of Natural and Mathematical Sciences, Massey Univ., Auckland, New Zealand