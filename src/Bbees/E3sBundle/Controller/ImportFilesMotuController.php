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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Bbees\E3sBundle\Services\ImportFileCsv;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
* ImportIndividu controller.
*
* @Route("importfilesmotu")
* @Security("has_role('ROLE_PROJECT')")
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
        // load the ImportFileE3s service
        $importFileE3sService = $this->get('bbees_e3s.import_file_e3s');
        $translator = $this->get('translator.default');
        //create form
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if($user->getRole() == 'ROLE_ADMIN') {
            $form = $this->createFormBuilder()
                    ->setMethod('POST')
                    ->add('motuFk',EntityType::class, array('class' => 'BbeesE3sBundle:Motu',
                         'query_builder' => function (EntityRepository $er) {
                                return $er->createQueryBuilder('motu')
                                   ->leftJoin('BbeesE3sBundle:Assigne', 'assigne', 'WITH', 'assigne.motuFk = motu.id')
                                   ->where('assigne.id IS NULL') 
                                   ;
                            }, 
                        'placeholder' => 'MOTU', 'choice_label' => 'nomFichierCsv', 'multiple' => false, 'expanded' => false))  
                    ->add('fichier', FileType::class)
                    ->add('envoyer', SubmitType::class, array('label' => 'Envoyer'))
                    ->getForm();         
        }
        $form->handleRequest($request);
        
        if ($form->isSubmitted()){ //processing form request 
            $fichier = $form->get('fichier')->getData()->getRealPath(); // path to the tmp file created
            $this->type_csv = "MOTU";
            $nom_fichier_download = $form->get('fichier')->getData()->getClientOriginalName();
            $message = "Import : ".$nom_fichier_download." ( Template ".$this->type_csv.".csv )<br />";
            // test if the file imported match the good columns name of the template file
            $pathToTemplate = $this->get('kernel')->getRootDir(). '/../web/template/'.$this->type_csv.'.csv';
            $service = $this->get('bbees_e3s.import_file_csv');
            $checkName = $translator->trans($service->checkNameCSVfile2Template($pathToTemplate , $fichier));
            $message .= $checkName;
            if($checkName  == ''){
                switch ($this->type_csv) {
                    case 'MOTU':
                        if ($form->get('fichier')->getData() !== NULL) {
                            // suppression des donnéee assignées 
                            //var_dump($form->get('motuFk')->getData()); exit;
                            $message .= $importFileE3sService->importCSVDataMotuFile($fichier,$form->get('motuFk')->getData(), $user->getId() );
                        } else {
                            $message .= "ERROR : <b>l'importation n a pas été effectué car le fichier de données de motu n'a pas été downloader</b>";
                        }
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
