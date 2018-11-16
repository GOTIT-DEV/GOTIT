<?php

namespace Bbees\E3sBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\Common\Annotations\AnnotationReader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
* ImportIndividu controller.
*
* @Route("importfilescollecte")
* @Security("has_role('ROLE_PROJECT')")
*/
class ImportFilesCollecteController extends Controller
{
    /**
    * @var string
    */
    private $type_csv;
     
    /**
    * @Route("/", name="importfilescollecte_index")
    *    
    */
     public function indexAction(Request $request)
    {    
        $message = ""; 
        // récuperation du service ImportFileE3s
        $importFileE3sService = $this->get('bbees_e3s.import_file_e3s');
        //creation du formulaire / ROLES
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if($user->getRole() == 'ROLE_ADMIN') {
        $form = $this->createFormBuilder()
                ->setMethod('POST')
                ->add('type_csv', ChoiceType::class, array(
                     'choice_translation_domain' => false,
                     'choices'  => array(
                         ' ' => array('Sampling' => 'collecte',),
                         '  ' => array('Program' => 'programme','Person' => 'personne',),
                         '   ' => array('Taxon' => 'referentiel_taxon','Vocabulary' => 'vocabulaire',),
                         ),
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
                         ' ' => array('Sampling' => 'collecte',),
                         '  ' => array('Program' => 'programme','Person' => 'personne',),
                         ),
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
            $message = "Import : ".$nom_fichier_download."<br />";
            switch ($this->type_csv) {
                case 'collecte':
                    $message .= $importFileE3sService->importCSVDataCollecte($fichier, $user->getId() );
                    break;
                case 'vocabulaire':
                    $message .= $importFileE3sService->importCSVDataVoc($fichier, $user->getId() );
                    break;
                case 'programme' :
                    $message .= $importFileE3sService->importCSVDataProgramme($fichier, $user->getId() );
                    break;
                case 'referentiel_taxon':
                    $message .= $importFileE3sService->importCSVDataReferentielTaxon($fichier, $user->getId() );
                    break;
                case 'personne' :
                    $message .= $importFileE3sService->importCSVDataPersonne($fichier, $user->getId() );
                    break;
                default:
                   $message .= "!  Le choix de la liste de fichier à importer ne correspond a aucun cas ?";
            }
            return $this->render('importfilecsv/importfiles.html.twig', array("message" => $message, 'form' => $form->createView())); 
        }
        return $this->render('importfilecsv/importfiles.html.twig', array("message" => $message,'form' => $form->createView()));   
     }   
}
