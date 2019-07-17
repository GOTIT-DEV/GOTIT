GOTIT1-1
=======
A Symfony project created on 03/12/2018 : Publish Version 1.1

- internationalisation des noms de colonnes des liens des listes d'enregistrements
- ajout contrainte Required pour le champ source (is published in) du formulaire sequenceassembleeext 

 nouvelle integration et gestion des formualaire espece_identifiée et des formulaire imbriqué Personne
- nouvelle gestion de la variable index dans la fn addArrayCollectionButton() et dans les appels après le submit du nouvel enregistrement (via les bouttons add new : modal)

- mise à jour des dictionnaire help : aide des procédure d'import des données referentielles (NB- version En à traduire en Fr)
- checkNameCSVfile2Template($pathToTemplate, $pathToFileImport),  test sur les nom de collonnes  des fichier CSV / template pour les  imports par lot

- upgrade symfony 3.4.1 => 3.4.19 (test security OK) 

- gestion du multilinguisme
- internationalisation des scripts de migration (messages d'erreur + commentaires Anglais )
- templates TWIG du dosier views  ; suppression des templates innutilisés, nettoyage des appels innutiles (base.html.twig), internationalisation des commentaires (Anglais)
- ajout de procédure d'import par lot des tables référentielles (cf. ImportFile*Controller + bouton Import new set * dans index.html.twig)
- ajout d'un template et d'une procédure d'import par lot pour les communes au niveau des données referentielle et pour les fichiers de Station (cf. ImportFileCommuneController, importFileE3s, importFilesStationController + template municipality.csv)

- ajout form-theme : colorisation en bleu des champs obligatoire
- ajout licence GNU GPL3 (Controller)
- internationalisation des controller

update 05/07/2019 : Version 1.1.1

- Add a field in english version of the vocabulary list with the translation of the title (label) store in the database (see translations in message.en.yml - vocParent.*)
- Add a selected list for the class of vocabulary (field "parent" from Voc entity) in the vocabulary form (VocType.php). The selected list labels are defined in messages.fr.yml/messages.en.yml for fr/en langages (/views/voc)
- Better english integration for YES/NO (0/1) fields in forms (see Sampling and Lot forms) 
- Modify the import scripts to accept YES and NO values (or 1 and 0)  ​for the status field of the Sampling and Biological material tables (see importFileE3s services)
- Reorganization and English comments of dictionaries (messages.fr.yml, messages.en.yml) for better readability and use in English version

update 08/07/2019 : Version 1.1.1
- bug fixed in the auto genarated code Sammple code when month are input like '0X' 
- add the possibility to take English label for date precision vocabulary {MONTH, YEAR, DAY, NOT KNOWN} (see view/collecte/edit.html.twig)
- Modify the import scripts to accept YES and NO values (or 1/0, OUI/NON)  ​for the status field of the Biological material tables (see importFileE3s services)
- bug fix in the import scripts ​for the status field of the Sampling and Biological material tables (see importFileE3s services)