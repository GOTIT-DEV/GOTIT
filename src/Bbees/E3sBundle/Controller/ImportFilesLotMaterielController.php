<?php

namespace Bbees\E3sBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


/**
* ImportIndividu controller.
*
* @Route("importfileslotmateriel")
*/
class ImportFilesLotMaterielController extends Controller
{
     /**
     * @var string
     */
    private $type_csv;
     
    /**
     * @Route("/", name="importfileslotmateriel_index")
     *    
     */
     public function indexAction(Request $request)
    {  
        $message = ""; 
        // récuperation du service ImportFileE3s
        $importFileE3sService = $this->get('bbees_e3s.import_file_e3s');
        //creation du formulaire
        $form = $this->createFormBuilder()
                ->setMethod('POST')
                ->add('type_csv', ChoiceType::class, array(
                    'choice_translation_domain' => false,
                    'choices'  => array(
                         ' ' => array('Lot materiel' => 'lot_materiel',),
                         '  ' => array('Boite' => 'boite',),
                         '   ' => array('Réferentiel taxon' => 'referentiel_taxon','Vocabulaire' => 'vocabulaire','Personne' => 'personne',),)
                    ))
                ->add('fichier', FileType::class)
                ->add('envoyer', SubmitType::class, array('label' => 'Envoyer'))
                ->getForm();
        $form->handleRequest($request);
        
        if ($form->isSubmitted()){ //recuperation des données et traitement 
            $fichier = $form->get('fichier')->getData()->getRealPath(); // La fonction getRealPath donne le chemin vers le fichier temporaire créé
            $this->type_csv = $form->get('type_csv')->getData();
            $nom_fichier_download = $form->get('fichier')->getData()->getClientOriginalName();
            $message = "Traitement du fichier : ".$nom_fichier_download."<br />";
            switch ($this->type_csv) {
                case 'lot_materiel':
                    $message .= $importFileE3sService->importCSVDataLotMateriel($fichier);
                    break;
                case 'vocabulaire':
                    $message .= $importFileE3sService->importCSVDataVoc($fichier);
                    break;
                case 'boite' :
                    $message .= $importFileE3sService->importCSVDataBoite($fichier);
                    break;
                case 'referentiel_taxon':
                    $message .= $importFileE3sService->importCSVDataReferentielTaxon($fichier);
                    break;
                case 'personne' :
                    $message .= $importFileE3sService->importCSVDataPersonne($fichier);
                    break;
                default:
                   $message .= "Le choix de la liste de fichier à importer ne correspond a aucun cas ?";
            }
            return $this->render('importfilecsv/importfiles.html.twig', array("message" => $message, 'form' => $form->createView())); 
        }
        return $this->render('importfilecsv/importfiles.html.twig', array("message" => $message,'form' => $form->createView()));   
     }  
}