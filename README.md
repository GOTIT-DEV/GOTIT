GOTIT1-1
=======
A Symfony project created on 03/12/2018 : Publish Version 1.1

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