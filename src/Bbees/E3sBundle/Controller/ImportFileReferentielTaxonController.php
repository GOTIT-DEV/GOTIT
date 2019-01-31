<?php

/*
 * This file is part of the E3sBundle.
 *
 * Copyright (c) 2018 Philippe Grison <philippe.grison@mnhn.fr>
 *
 * E3sBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * E3sBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with E3sBundle.  If not, see <https://www.gnu.org/licenses/>
 * 
 */

namespace Bbees\E3sBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Bbees\E3sBundle\Services\ImportFileCsv;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
* Import Voc controller.
*
* @Route("importfilesreferentieltaxon")
* @Security("has_role('ROLE_ADMIN')")
*/
class ImportFileReferentielTaxonController extends Controller 
{     
    /**
     * @Route("/", name="importfilesreferentieltaxon_index")
     *    
     */
     public function indexAction(Request $request)
    {     
        $message = ""; 
        // load the ImportFileE3s service
        $importFileE3sService = $this->get('bbees_e3s.import_file_e3s');
        $translator = $this->get('translator.default');
        //creation of the form with a drop-down list
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if($user->getRole() == 'ROLE_ADMIN') {
            $form = $this->createFormBuilder()
                ->setMethod('POST')
                ->add('type_csv', ChoiceType::class, array(
                     'choice_translation_domain' => false,
                     'choices'  => array(
                         ' ' => array('Taxon' => 'taxon'),)
                    ))
                ->add('fichier', FileType::class)
                ->add('envoyer', SubmitType::class, array('label' => 'Envoyer'))
                ->getForm();            
        }
        $form->handleRequest($request); 
        
        if ($form->isSubmitted()){ //processing form request 
            $fichier = $form->get('fichier')->getData()->getRealPath(); // path to the tmp file created
            $this->type_csv = $form->get('type_csv')->getData();
            $nom_fichier_download = $form->get('fichier')->getData()->getClientOriginalName();
            $message = "Import : ".$nom_fichier_download." ( Template ".$this->type_csv.".csv )<br />";
            // test if the file imported match the good columns name of the template file
            $pathToTemplate = $this->get('kernel')->getRootDir(). '/../web/template/'.$this->type_csv.'.csv';
            $service = $this->get('bbees_e3s.import_file_csv');
            $checkName = $translator->trans($service->checkNameCSVfile2Template($pathToTemplate , $fichier));
            $message .= $checkName;
            if($checkName  == ''){
                switch ($this->type_csv) {
                    case 'taxon':
                        $message .= $importFileE3sService->importCSVDataReferentielTaxon($fichier, $user->getId() );
                        break;
                    default:
                       $message .=  "ERROR - Bad SELECTED choice ?";
                }
            }
            return $this->render('importfilecsv/importfiles.html.twig', array("message" => $message, 'form' => $form->createView())); 
        }
        return $this->render('importfilecsv/importfiles.html.twig', array("message" => $message,'form' => $form->createView()));  
    } 
    
}
