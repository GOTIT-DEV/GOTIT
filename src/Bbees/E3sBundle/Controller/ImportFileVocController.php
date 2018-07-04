<?php

namespace Bbees\E3sBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
* Import Voc controller.
*
* @Route("importfilevoc")
*/
class ImportFileVocController extends Controller 
{     
    /**
     * @Route("/", name="importfilevoc_index")
     *    
     */
     public function indexAction(Request $request)
    {            
        $message = "";
        return $this->render('importfilecsv/voc.html.twig', array("message" => $message)); 
        
    }   
    
     /**
     * Finds and displays le formulaire dimport d'une voc .
     *
     * @Route("/show", name="importfilevoc_show")
     * 
     */
    public function showAction(Request $request)
    {
        $message = "";
        $type_template_csv = "Voc";
        // récuperation du service ImportFileE3s
        $importFileE3sService = $this->get('bbees_e3s.import_file_e3s');
        //creation du formulaire : checkbox
        $form = $this->createFormBuilder()
                ->add('fichier', FileType::class)
                ->add('submitVoc', SubmitType::class, array('label' => 'Envoyer'))
                ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->get('submitVoc')->isClicked()){ //recuperation des données et traitement 
            $fichier = $form->get('fichier')->getData()->getRealPath(); // La fonction getRealPath donne le chemin vers le fichier temporaire créé
            $message = $importFileE3sService->importCSVDataVoc($fichier);
            return $this->render('importfilecsv/form.html.twig', array("message" => $message, "type_template_csv" => $type_template_csv,'form' => $form->createView()));
        }
        return $this->render('importfilecsv/form.html.twig', array("message" => $message,  "type_template_csv" => $type_template_csv,'form' => $form->createView())); 
    }
}
