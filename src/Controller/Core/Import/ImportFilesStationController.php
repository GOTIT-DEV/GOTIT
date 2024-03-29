<?php

namespace App\Controller\Core\Import;

use App\Services\Core\ImportFileCsv;
use App\Services\Core\ImportFileE3s;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\EntityController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Import files station controller.
 *
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
#[Route("importfilesstation")]
#[IsGranted("ROLE_PROJECT")]
class ImportFilesStationController extends EntityController {

  #[Route("/", name: "importfilesstation_index")]
  public function indexAction(
    Request $request,
    ImportFileE3s $importFileE3sService,
    TranslatorInterface $translator,
    ImportFileCsv $service
  ) {
    $message = '';
    //creation of the form with a drop-down list
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if ($user->getRole() == 'ROLE_ADMIN') {
      $form = $this->createFormBuilder()
        ->setMethod('POST')
        ->add('type_csv', ChoiceType::class, array(
          'choice_translation_domain' => false,
          'choices' => array(
            ' ' => array('Site' => 'site'),
            '  ' => array('Country' => 'country', 'Municipality' => 'municipality', 'Vocabulary' => 'vocabulary'),
          ),
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
          'choices' => array(
            ' ' => array('Site' => 'site'),
          ),
        ))
        ->add('fichier', FileType::class)
        ->add('envoyer', SubmitType::class, array('label' => 'Envoyer'))
        ->getForm();
    }
    $form->handleRequest($request);

    if ($form->isSubmitted()) { //processing form request
      $fichier = $form->get('fichier')->getData()->getRealPath(); // path to the tmp file created
      $type_csv = $form->get('type_csv')->getData();
      $nom_fichier_download = $form->get('fichier')->getData()->getClientOriginalName();
      $message = "Import : " . $nom_fichier_download . " ( Template " . $type_csv . ".csv )<br />";
      // test if the file imported match the good columns name of the template file
      $pathToTemplate = $service->getCsvPath($type_csv);
      //
      $checkName = $translator->trans($service->checkNameCSVfile2Template($pathToTemplate, $fichier));
      $message .= $checkName;
      if ($checkName == '') {
        switch ($type_csv) {
          case 'country':
            $message .= $importFileE3sService->importCSVDataPays($fichier, $user->getId());
            break;
          case 'municipality':
            $message .= $importFileE3sService->importCSVDataCommune($fichier, $user->getId());
            break;
          case 'vocabulary':
            $message .= $importFileE3sService->importCSVDataVoc($fichier, $user->getId());
            break;
          case 'site':
            $message .= $importFileE3sService->importCSVDataStation($fichier, $user->getId());
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
