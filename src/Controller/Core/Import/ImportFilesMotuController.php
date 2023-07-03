<?php

namespace App\Controller\Core\Import;

use App\Services\Core\ImportFileCsv;
use App\Services\Core\ImportFileE3s;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\EntityController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Motu;

/**
 * ImportIndividu controller.
 *
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
#[Route("importfilesmotu")]
#[IsGranted("ROLE_ADMIN")]
class ImportFilesMotuController extends EntityController {

  #[Route("/", name: "importfilesmotu_index")]
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
        ->add('motuFk', EntityType::class, array(
          'class' => Motu::class,
          'query_builder' => function (EntityRepository $er) {
            return $er->createQueryBuilder('motu')
              ->leftJoin('App:Assigne', 'assigne', 'WITH', 'assigne.motuFk = motu.id')
              ->where('assigne.id IS NULL');
          },
          'placeholder' => 'MOTU', 'choice_label' => 'nomFichierCsv', 'multiple' => false, 'expanded' => false,
        ))
        ->add('fichier', FileType::class)
        ->add('envoyer', SubmitType::class, array('label' => 'Envoyer'))
        ->getForm();
    }
    $form->handleRequest($request);

    if ($form->isSubmitted()) { //processing form request
      $fichier = $form->get('fichier')->getData()->getRealPath(); // path to the tmp file created
      $type_csv = "MOTU";
      $nom_fichier_download = $form->get('fichier')->getData()->getClientOriginalName();
      $message = "Import : " . $nom_fichier_download . " ( Template " . $type_csv . ".csv )<br />";
      // test if the file imported match the good columns name of the template file
      $pathToTemplate = $service->getCsvPath($type_csv);
      //
      $checkName = $translator->trans($service->checkNameCSVfile2Template($pathToTemplate, $fichier));
      $message .= $checkName;
      if ($checkName == '') {
        switch ($type_csv) {
          case 'MOTU':
            if ($form->get('fichier')->getData() !== NULL) {
              // suppression des donnéee assignées
              $message .= $importFileE3sService->importCSVDataMotuFile($fichier, $form->get('motuFk')->getData(), $user->getId());
            } else {
              $message .= "ERROR : <b>l'importation n a pas été effectué car le fichier de données de motu n'a pas été downloader</b>";
            }
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
