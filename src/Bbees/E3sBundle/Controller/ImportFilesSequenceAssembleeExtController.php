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
* @Route("importfilessequenceassembleeext")
*/
class ImportFilesSequenceAssembleeExtController extends Controller
{
     /**
     * @var string
     */
    private $type_csv;
     
    /**
     * @Route("/", name="importfilessequenceassembleeext_index")
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
                         ' ' => array('Sequence assemblee ext' => 'sequence_assemblee_ext'),
                         '  ' => array( 'Station' => 'station', 'Collecte' => 'collecte', 'Source' => 'source',),
                         '   ' => array('Vocabulaire' => 'vocabulaire','Personne' => 'personne','Referentiel taxon' => 'referentiel_taxon','Pays' => 'pays',),)
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
                case 'sequence_assemblee_ext':
                    $message .= $importFileE3sService->importCSVDataSequenceAssembleeExt($fichier);
                    break;
                case 'source':
                    $message .= $importFileE3sService->importCSVDataSource($fichier);
                    break;
                case 'vocabulaire':
                    $message .= $importFileE3sService->importCSVDataVoc($fichier);
                    break;
                case 'referentiel_taxon' :
                    $message .= $importFileE3sService->importCSVDataReferentielTaxon($fichier);
                    break;
                case 'personne' :
                    $message .= $importFileE3sService->importCSVDataPersonne($fichier);
                    break;
                 case 'pays':
                    $message .= $importFileE3sService->importCSVDataPays($fichier);
                    break;
                case 'station' :
                    $message .= $importFileE3sService->importCSVDataStation($fichier);
                    break;
                case 'collecte' :
                    $message .= $importFileE3sService->importCSVDataCollecte($fichier);
                    break;
                default:
                   $message .=  "Le choix de la liste de fichier à importer ne correspond a aucun cas ?";
            }
            return $this->render('importfilecsv/importfiles.html.twig', array("message" => $message, 'form' => $form->createView())); 
        }
        return $this->render('importfilecsv/importfiles.html.twig', array("message" => $message,'form' => $form->createView())); 
     }    
}
