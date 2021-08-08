<?php

namespace App\Controller\Import;

use App\Services\Core\ImportFileCsv;
use App\Services\Core\ImportFileE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * ImportIndividu controller.
 *
 * @Route("importfilessqcassembleepublie")
 * @Security("is_granted('ROLE_COLLABORATION')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class ImportFilesSqcAssembleePublieController extends AbstractController {
  /**
   * @var string
   */
  private $type_csv;

  /**
   * @Route("/", name="importfilessqcassembleepublie_index")
   *
   */
  public function indexAction(
    Request $request,
    ImportFileE3s $importFileE3sService,
    TranslatorInterface $translator,
    ImportFileCsv $service
  ) {
    $message = "";
    //creation of the form with a drop-down list
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if ($user->getRole() == 'ROLE_ADMIN' || $user->getRole() == 'ROLE_PROJECT') {
      $form = $this->createFormBuilder()
        ->setMethod('POST')
        ->add('type_csv', ChoiceType::class, array(
          'choice_translation_domain' => false,
          'choices' => array(
            ' ' => array('Source_attribute_to_sequence' => 'source_attribute_to_sequence'),
            '  ' => array('Source' => 'source', 'Person' => 'person'),
          ),
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
          'choices' => array(
            ' ' => array('Source_attribute_to_sequence' => 'source_attribute_to_sequence'),
            '  ' => array('Person' => 'person'),
          ),
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
      if ($checkName == '') {
        switch ($this->type_csv) {
        case 'source_attribute_to_sequence':
          $message .= $importFileE3sService->importCSVDataSqcAssembleePublie($fichier, $user->getId());
          break;
        case 'source':
          $message .= $importFileE3sService->importCSVDataSource($fichier, $user->getId());
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
