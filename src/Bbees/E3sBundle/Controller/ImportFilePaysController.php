<?php

namespace Bbees\E3sBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
* Import Pays controller.
*
* @Route("importfilepays")
*/
class ImportFilePaysController extends Controller 
{     
    /**
     * @Route("/", name="importfilepays_index")
     *    
     */
     public function indexAction(Request $request)
    {            
        $message = "";
        return $this->render('importfilecsv/pays.html.twig', array("message" => $message)); 
        
    }   
    
     /**
     * Finds and displays le formulaire dimport d'une pays .
     *
     * @Route("/show", name="importfilepays_show")
     * 
     */
    public function showAction(Request $request)
    {
        $message = "";
        $type_template_csv = "Pays";
        // récuperation du service ImportFileE3s
        $importFileE3sService = $this->get('bbees_e3s.import_file_e3s');
        //creation du formulaire : checkbox
        $form = $this->createFormBuilder()
                ->add('fichier', FileType::class)
                ->add('submitPays', SubmitType::class, array('label' => 'Envoyer'))
                ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->get('submitPays')->isClicked()){ //recuperation des données et traitement 
            $fichier = $form->get('fichier')->getData()->getRealPath(); // La fonction getRealPath donne le chemin vers le fichier temporaire créé
            $message = $importFileE3sService->importCSVDataPays($fichier);
            return $this->render('importfilecsv/form.html.twig', array("message" => $message, "type_template_csv" => $type_template_csv,'form' => $form->createView()));
        }
        return $this->render('importfilecsv/form.html.twig', array("message" => $message,  "type_template_csv" => $type_template_csv,'form' => $form->createView())); 
    }
}
