<?php

namespace Bbees\E3sBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
* ImportIndividu controller.
*
* @Route("importfilesmotu")
*/
class ImportFilesMotuController extends Controller
{
     /**
     * @var string
     */
    private $type_csv;
     
    /**
     * @Route("/", name="importfilesmotu_index")
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
                ->add('motuFk',EntityType::class, array('class' => 'BbeesE3sBundle:Motu',
                     'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('motu')
                               ->leftJoin('BbeesE3sBundle:Assigne', 'assigne', 'WITH', 'assigne.motuFk = motu.id')
                               ->where('assigne.id IS NULL') 
                               ;
                        }, 
                    'placeholder' => 'Choose a Motu File', 'choice_label' => 'nomFichierCsv', 'multiple' => false, 'expanded' => false))  
/*                ->add('type_csv', ChoiceType::class, array(
                    'choice_translation_domain' => false,
                    'choices'  => array(
                         ' ' => array('Motu' => 'motu',),
                         '   ' => array('Vocabulaire' => 'vocabulaire','Personne' => 'personne',),)
                    ))
*/
                ->add('fichier', FileType::class)
                ->add('envoyer', SubmitType::class, array('label' => 'Envoyer'))
                ->getForm();
        $form->handleRequest($request);
        
        if ($form->isSubmitted()){ //recuperation des données et traitement 
            $fichier = $form->get('fichier')->getData()->getRealPath(); // La fonction getRealPath donne le chemin vers le fichier temporaire créé
            // var_dump($fichier_motu); exit;
            //$this->type_csv = $form->get('type_csv')->getData();
            $this->type_csv = "motu";
            $nom_fichier_download = $form->get('fichier')->getData()->getClientOriginalName();
            $message = "Traitement du fichier : ".$nom_fichier_download."<br />";
            switch ($this->type_csv) {
                case 'motu':
                    if ($form->get('fichier')->getData() !== NULL) {
                        // suppression des donnéee assignées 
                        //var_dump($form->get('motuFk')->getData()); exit;
                        $message .= $importFileE3sService->importCSVDataMotuFile($fichier,$form->get('motuFk')->getData() );
                    } else {
                        $message .= "ERROR : <b>l'importation n a pas été effectué car le fichier de données de motu n'a pas été downloader</b>";
                    }
                    break;
                case 'vocabulaire':
                    $message .= $importFileE3sService->importCSVDataVoc($fichier);
                    break;
                case 'personne':
                    $message .= $importFileE3sService->importCSVDataPersonne($fichier);
                    break;
                default:
                   $message .=  "Le choix de la liste de fichier à importer ne correspond a aucun cas ?";
            }
            return $this->render('importfilecsv/importfiles.html.twig', array("message" => $message, 'form' => $form->createView())); 
        }
        return $this->render('importfilecsv/importfiles.html.twig', array("message" => $message,'form' => $form->createView())); 
     }
}
