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
* @Route("importfilestation")
*/
class ImportFileStationController extends Controller 
{     
    /**
     * @Route("/", name="importfilestation_index")
     *    
     */
     public function indexAction(Request $request)
    {            
        $message = "";
        
        return $this->render('importfilecsv/station.html.twig', array("message" => $message)); 
        
    }   
    
     /**
     * Finds and displays le formulaire dimport d'une station .
     *
     * @Route("/show", name="importfilestation_show")
     * 
     */
    public function showAction(Request $request)
    {
        $message = "";
        $type_template_csv = "Station";
        // récuperation du service ImportFileE3s
        $importFileE3sService = $this->get('bbees_e3s.import_file_e3s');
        //creation du formulaire : checkbox
        $form = $this->createFormBuilder()
                ->add('fichier', FileType::class)
                ->add('submitStation', SubmitType::class, array('label' => 'Envoyer'))
                ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->get('submitStation')->isClicked()){ //recuperation des données et traitement 
            $fichier = $form->get('fichier')->getData()->getRealPath(); // La fonction getRealPath donne le chemin vers le fichier temporaire créé
            $message = $importFileE3sService->importCSVDataStation($fichier);
            return $this->render('importfilecsv/form.html.twig', array("message" => $message, "type_template_csv" => $type_template_csv,'form' => $form->createView()));
        }
        return $this->render('importfilecsv/form.html.twig', array("message" => $message, "type_template_csv" => $type_template_csv,'form' => $form->createView())); 
    }
}
