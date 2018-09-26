<?php

namespace Bbees\E3sBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
* ImportIndividu controller.
*
* @Route("importfilessequenceassembleeext")
* @Security("has_role('ROLE_COLLABORATION')")
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

        //creation du formulaire / ROLES
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if($user->getRole() == 'ROLE_ADMIN') {
        $form = $this->createFormBuilder()
                ->setMethod('POST')
                ->add('type_csv', ChoiceType::class, array(
                    'choice_translation_domain' => false,
                    'choices'  => array(
                         ' ' => array('External_sequence' => 'sequence_assemblee_ext'),
                         '  ' => array('Source' => 'source',),
                         '   ' => array('Vocabulary' => 'vocabulaire','Person' => 'personne','Taxon' => 'referentiel_taxon',),)
                    ))
                ->add('fichier', FileType::class)
                ->add('envoyer', SubmitType::class, array('label' => 'Envoyer'))
                ->getForm();      
        }
        if($user->getRole() == 'ROLE_PROJECT') {
        $form = $this->createFormBuilder()
                ->setMethod('POST')
                ->add('type_csv', ChoiceType::class, array(
                    'choice_translation_domain' => false,
                    'choices'  => array(
                         ' ' => array('External_sequence' => 'sequence_assemblee_ext'),
                         '  ' => array('Source' => 'source',),
                         '   ' => array('Person' => 'personne',),)
                    ))
                ->add('fichier', FileType::class)
                ->add('envoyer', SubmitType::class, array('label' => 'Envoyer'))
                ->getForm();        
        } 
        if($user->getRole() == 'ROLE_COLLABORATION') {
        $form = $this->createFormBuilder()
                ->setMethod('POST')
                ->add('type_csv', ChoiceType::class, array(
                    'choice_translation_domain' => false,
                    'choices'  => array(
                         ' ' => array('External_sequence' => 'sequence_assemblee_ext'),
                         '   ' => array('Person' => 'personne',),)
                    ))
                ->add('fichier', FileType::class)
                ->add('envoyer', SubmitType::class, array('label' => 'Envoyer'))
                ->getForm();    
        }
        $form->handleRequest($request);
        
        if ($form->isSubmitted()){ //recuperation des données et traitement 
            $fichier = $form->get('fichier')->getData()->getRealPath(); // La fonction getRealPath donne le chemin vers le fichier temporaire créé
            $this->type_csv = $form->get('type_csv')->getData();
            $nom_fichier_download = $form->get('fichier')->getData()->getClientOriginalName();
            $message = "Traitement du fichier : ".$nom_fichier_download."<br />";
            switch ($this->type_csv) {
                case 'sequence_assemblee_ext':
                    $message .= $importFileE3sService->importCSVDataSequenceAssembleeExt($fichier, $user->getId());
                    break;
                case 'source':
                    $message .= $importFileE3sService->importCSVDataSource($fichier, $user->getId());
                    break;
                case 'vocabulaire':
                    $message .= $importFileE3sService->importCSVDataVoc($fichier, $user->getId());
                    break;
                case 'referentiel_taxon' :
                    $message .= $importFileE3sService->importCSVDataReferentielTaxon($fichier, $user->getId());
                    break;
                case 'personne' :
                    $message .= $importFileE3sService->importCSVDataPersonne($fichier, $user->getId());
                    break;
                 case 'pays':
                    $message .= $importFileE3sService->importCSVDataPays($fichier, $user->getId());
                    break;
                case 'station' :
                    $message .= $importFileE3sService->importCSVDataStation($fichier, $user->getId());
                    break;
                case 'collecte' :
                    $message .= $importFileE3sService->importCSVDataCollecte($fichier, $user->getId());
                    break;
                default:
                   $message .=  "Le choix de la liste de fichier à importer ne correspond a aucun cas ?";
            }
            return $this->render('importfilecsv/importfiles.html.twig', array("message" => $message, 'form' => $form->createView())); 
        }
        return $this->render('importfilecsv/importfiles.html.twig', array("message" => $message,'form' => $form->createView())); 
     }    
}
