GOTIT1-1
=======
A Symfony project created on 03/12/2018 : Dev version 1.1.2 / Last publish release  v1.1.1

update 08/10/2019
- fix the issue #1 : "bad selected country value when you add a new municipality from an existing site form"
- include the last version of the bundle SpeciesSearchBundle v1.1 
- update font-awesome to v5
- update documentation GOTIT_install.pdf and csv file vocabulary_gotit1-1


Publish / Release Version 1.1.1 : [https://github.com/GOTIT-DEV/GOTIT/releases/tag/v1.1.1]v1.1.1

update 24/07/2019 
- Last changes in dictionaries messages : ex. change  "Ind/St" by "Ind/Si" (cf. Biological Material list) ...
- Change in the external sequence list "Date precision Code" by "Date precision Libelle"
- Update of documentations for the Version 1.1.1

update 08/07/2019 
- bug fixed in the auto genarated code Sammple code when month are input like '0X' 
- add the possibility to take English label for date precision vocabulary {MONTH, YEAR, DAY, NOT KNOWN} (see view/collecte/edit.html.twig)
- Modify the import scripts to accept YES and NO values (or 1/0, OUI/NON)  ​for the status field of the Biological material tables (see importFileE3s services)
- bug fix in the import scripts ​for the status field of the Sampling and Biological material tables (see importFileE3s services)

update 05/07/2019 

- Add a field in english version of the vocabulary list with the translation of the title (label) store in the database (see translations in message.en.yml - vocParent.*)
- Add a selected list for the class of vocabulary (field "parent" from Voc entity) in the vocabulary form (VocType.php). The selected list labels are defined in messages.fr.yml/messages.en.yml for fr/en langages (/views/voc)
- Better english integration for YES/NO (0/1) fields in forms (see Sampling and Lot forms) 
- Modify the import scripts to accept YES and NO values (or 1 and 0)  ​for the status field of the Sampling and Biological material tables (see importFileE3s services)
- Reorganization and English comments of dictionaries (messages.fr.yml, messages.en.yml) for better readability and use in English version
