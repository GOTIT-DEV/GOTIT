<?php

/*
 * This file is part of the E3sBundle.
 *
 * Authors : see information concerning authors of GOTIT project in file AUTHORS.md
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

namespace App\Controller\Core\Import;

use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Services\Core\ImportFileE3s;
use App\Services\Core\ImportFileCsv;


/**
 * ImportIndividu controller.
 *
 * @Route("importfilespcr")
 * @Security("has_role('ROLE_COLLABORATION')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 * 
 */
class ImportFilesPcrController extends AbstractController
{
  /**
   * @var string
   */
  private $type_csv;

  /**
   * @Route("/", name="importfilespcr_index")
   *    
   */
  public function indexAction(
    Request $request,
    ImportFileE3s $importFileE3sService,
    TranslatorInterface $translator,
    ImportFileCsv $service
  ) {
    $message = "";
    //create form
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if ($user->getRole() == 'ROLE_ADMIN') {
      $form = $this->createFormBuilder()
        ->setMethod('POST')
        ->add('type_csv', ChoiceType::class, array(
          'choice_translation_domain' => false,
          'choices'  => array(
            ' ' => array('PCR' => 'PCR',),
            '  ' => array('Vocabulary' => 'vocabulary', 'Person' => 'person',),
          )
        ))
        ->add('fichier', FileType::class)
        ->add('envoyer', SubmitType::class, array('label' => 'Envoyer'))
        ->getForm();
    }
    if ($user->getRole() == 'ROLE_PROJECT') {
      $form = $this->createFormBuilder()
        ->setMethod('POST')
        ->add('type_csv', ChoiceType::class, array(
          'choice_translation_domain' => false,
          'choices'  => array(
            ' ' => array('PCR' => 'PCR',),
            '  ' => array('Person' => 'person',),
          )
        ))
        ->add('fichier', FileType::class)
        ->add('envoyer', SubmitType::class, array('label' => 'Envoyer'))
        ->getForm();
    }
    if ($user->getRole() == 'ROLE_COLLABORATION') {
      $form = $this->createFormBuilder()
        ->setMethod('POST')
        ->add('type_csv', ChoiceType::class, array(
          'choice_translation_domain' => false,
          'choices'  => array(
            ' ' => array('PCR' => 'PCR',),
            '  ' => array('Person' => 'person',),
          )
        ))
        ->add('fichier', FileType::class)
        ->add('envoyer', SubmitType::class, array('label' => 'Envoyer'))
        ->getForm();
    }
    $form->handleRequest($request);

    if ($form->isSubmitted()) { //processing form request 
      $fichier = $form->get('fichier')->getData()->getRealPath(); // path to the tmp file created
      $this->type_csv = $form->get('type_csv')->getData();
      $nom_fichier_download = $form->get('fichier')->getData()->getClientOriginalName();
      $message = "Import : " . $nom_fichier_download . " ( Template " . $this->type_csv . ".csv )<br />";
      // test if the file imported match the good columns name of the template file
      $pathToTemplate = $service->getCsvPath($this->type_csv);
      // 
      $checkName = $translator->trans($service->checkNameCSVfile2Template($pathToTemplate, $fichier));
      $message .= $checkName;
      if ($checkName  == '') {
        switch ($this->type_csv) {
          case 'PCR':
            $message .= $importFileE3sService->importCSVDataPcr($fichier, $user->getId());
            break;
          case 'vocabulary':
            $message .= $importFileE3sService->importCSVDataVoc($fichier, $user->getId());
            break;
          case 'person':
            $message .= $importFileE3sService->importCSVDataPersonne($fichier, $user->getId());
            break;
          default:
            $message .= "ERROR - Bad SELECTED choice ?";
        }
      }
      return $this->render('Core/importfilecsv/importfiles.html.twig', array("message" => $message, 'form' => $form->createView()));
    }
    return $this->render('Core/importfilecsv/importfiles.html.twig', array("message" => $message, 'form' => $form->createView()));
  }
}
