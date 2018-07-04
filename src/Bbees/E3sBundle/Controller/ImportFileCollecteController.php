<?php

namespace Bbees\E3sBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
* Import Collecte controller.
*
* @Route("importfilecollecte")
*/
class ImportFileCollecteController extends Controller 
{     
    /**
     * @Route("/", name="importfilecollecte_index")
     *    
     */
     public function indexAction(Request $request)
    {            
        $message = "";
        return $this->render('importfilecsv/collecte.html.twig', array("message" => $message)); 
        
    }   
    
     /**
     * Finds and displays le formulaire dimport d'une collecte .
     *
     * @Route("/show", name="importfilecollecte_show")
     * 
     */
    public function showAction(Request $request)
    {
        $message = "";
        $type_template_csv = "Collecte";
        // récuperation du service ImportFileE3s
        $importFileE3sService = $this->get('bbees_e3s.import_file_e3s');
        //creation du formulaire : checkbox
        $form = $this->createFormBuilder()
                ->add('fichier', FileType::class)
                ->add('submitCollecte', SubmitType::class, array('label' => 'Envoyer'))
                ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->get('submitCollecte')->isClicked()){ //recuperation des données et traitement 
            $fichier = $form->get('fichier')->getData()->getRealPath(); // La fonction getRealPath donne le chemin vers le fichier temporaire créé
            $message = $importFileE3sService->importCSVDataCollecte($fichier);
            return $this->render('importfilecsv/form.html.twig', array("message" => $message, "type_template_csv" => $type_template_csv,'form' => $form->createView()));
        }
        return $this->render('importfilecsv/form.html.twig', array("message" => $message,  "type_template_csv" => $type_template_csv,'form' => $form->createView())); 
    }
}
