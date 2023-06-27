<?php

namespace App\Form;

use App\Entity\Pays;
use App\Entity\User;
use App\Form\ActionFormType;
use App\Form\Type\BaseVocType;
use App\Form\Type\CoordinateType;
use App\Form\Type\CountryVocType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\ModalButtonType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Controller\Core\CommuneController;
use App\Entity\Commune;

class StationType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $user = $this->security->getUser();
    $station = $builder->getData();
    $builder
      ->add('codeStation', EntityCodeType::class, [
        "disabled" => $this->canEditAdminOnly($options),
      ])
      ->add('nomStation');
    if ($user instanceof User) {
      $builder->add('infoDescription');
    }
    $builder->add('paysFk', CountryVocType::class)
      ->add('communeFk')
      ->add('newMunicipality', ModalButtonType::class, [
        'label' => 'button.NewCommune',
        'icon_class' => 'fa-plus-circle',
        'attr' => [
          'class' => "btn-info btn-sm",
          "data-modal-controller" => 'App\\Controller\\Core\\CommuneController::newmodalAction',
        ],
      ])
      ->add('habitatTypeVocFk', BaseVocType::class, array(
        'voc_parent' => 'habitatType',
        'placeholder' => 'Choose an Habitat Type',
      ))
      ->add('pointAccesVocFk', BaseVocType::class, array(
        'voc_parent' => 'pointAcces',
        'placeholder' => 'Choose an Access Point',
      ))
      ->add('latDegDec', CoordinateType::class, array(
        'required' => true,
        'html5' => true,
        'attr' => array(
          'min' => -90,
          'max' => 90,
          'step' => 0.00001,
        ),
      ))
      ->add('longDegDec', CoordinateType::class, array(
        'required' => true,
        'html5' => true,
        'attr' => array(
          'min' => -180,
          'max' => 180,
          'step' => 0.00001,
        ),
      ))
      ->add('showNearbySites', ModalButtonType::class, [
        'label' => 'button.showNearbyStations',
        'attr' => [
          'class' => "btn-info btn-sm",
          // 'data-target' => "#map-modal",
        ],
        "disabled" => true,
        'icon_class' => 'fa-crosshairs',
      ])
      ->add('precisionLatLongVocFk', BaseVocType::class, array(
        'voc_parent' => 'precisionLatLong',
        'placeholder' => 'Choose a GPS Distance Quality',
        "sort_by_id" => true,
      ))
      ->add('altitudeM');
    if ($user instanceof User) {
      $builder->add('commentaireStation');
    }
    $builder->addEventSubscriber($this->addUserDate);

    $this->upperCaseFields($builder, [
      'codeStation', 'nomStation',
    ]);

    $formModifier = function (FormInterface $form, Pays $paysFk = null) {
      $form
        // Commune
        ->add('communeFk', EntityType::class, array(
          'class' => Commune::class,
          'query_builder' => function (EntityRepository $er) use ($paysFk) {
            $query = $er->createQueryBuilder('commune')
              ->orderBy('commune.codeCommune', 'ASC');
            //var_dump($paysFk->getId());
            if ($paysFk) {
              $query = $query->where('commune.paysFk = :country')
                ->setParameter('country', $paysFk->getId());
            }
            return $query;
          },
          'choice_label' => 'codeCommune',
          'multiple' => false,
          'expanded' => false,
          'placeholder' => 'Choose a Commune',
        ));
    };

    $builder->addEventListener(
      FormEvents::PRE_SET_DATA,
      function (FormEvent $event) use ($formModifier) {
        // this would be your entity, i.e. Station
        $data = $event->getData();

        $formModifier($event->getForm(), $data->getPaysFk());
      }
    );

    $builder->get('paysFk')->addEventListener(
      FormEvents::POST_SUBMIT,
      function (FormEvent $event) use ($formModifier) {
        // It's important here to fetch $event->getForm()->getData(), as
        // $event->getData() will get you the client data (that is, the ID)
        $paysFk = $event->getForm()->getData();

        // since we've added the listener to the child, we'll have to pass on
        // the parent to the callback function!
        $formModifier($event->getForm()->getParent(), $paysFk);
      }
    );
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Station',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix(): string {
    return 'station';
  }
}
