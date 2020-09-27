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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\ORM\EntityRepository;
use App\Services\Core\ImportFileE3s;
use App\Services\Core\ImportFileCsv;


/**
 * ImportIndividu controller.
 *
 * @Route("importfilesmotu")
 * @Security("has_role('ROLE_PROJECT')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class ImportFilesMotuController extends AbstractController
{
  /**
   * @var string
   */
  private $type_csv;

  /**
   * @Route("/", name="importfilesmotu_index")
   *    
   */
  public function indexAction(Request $request, ImportFileE3s $importFileE3sService, TranslatorInterface $translator, ImportFileCsv $service)
  {
    $message = "";
    //create form
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if ($user->getRole() == 'ROLE_ADMIN') {
      $form = $this->createFormBuilder()
        ->setMethod('POST')
        ->add('motuFk', EntityType::class, array(
          'class' => 'App:Motu',
          'query_builder' => function (EntityRepository $er) {
            return $er->createQueryBuilder('motu')
              ->leftJoin('App:Assigne', 'assigne', 'WITH', 'assigne.motuFk = motu.id')
              ->where('assigne.id IS NULL');
          },
          'placeholder' => 'MOTU', 'choice_label' => 'nomFichierCsv', 'multiple' => false, 'expanded' => false
        ))
        ->add('fichier', FileType::class)
        ->add('envoyer', SubmitType::class, array('label' => 'Envoyer'))
        ->getForm();
    }
    $form->handleRequest($request);

    if ($form->isSubmitted()) { //processing form request 
      $fichier = $form->get('fichier')->getData()->getRealPath(); // path to the tmp file created
      $this->type_csv = "MOTU";
      $nom_fichier_download = $form->get('fichier')->getData()->getClientOriginalName();
      $message = "Import : " . $nom_fichier_download . " ( Template " . $this->type_csv . ".csv )<br />";
      // test if the file imported match the good columns name of the template file
      $pathToTemplate = $service->getCsvPath($this->type_csv);
      // 
      $checkName = $translator->trans($service->checkNameCSVfile2Template($pathToTemplate, $fichier));
      $message .= $checkName;
      if ($checkName  == '') {
        switch ($this->type_csv) {
          case 'MOTU':
            if ($form->get('fichier')->getData() !== NULL) {
              // suppression des donnéee assignées 
              //var_dump($form->get('motuFk')->getData()); exit;
              $message .= $importFileE3sService->importCSVDataMotuFile($fichier, $form->get('motuFk')->getData(), $user->getId());
            } else {
              $message .= "ERROR : <b>l'importation n a pas été effectué car le fichier de données de motu n'a pas été downloader</b>";
            }
            break;
          default:
            $message .=  "ERROR - Bad SELECTED choice ?";
        }
      }
      return $this->render('Core/importfilecsv/importfiles.html.twig', array("message" => $message, 'form' => $form->createView()));
    }
    return $this->render('Core/importfilecsv/importfiles.html.twig', array("message" => $message, 'form' => $form->createView()));
  }
}
