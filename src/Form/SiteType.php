<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\Type\BaseVocType;
use App\Form\Type\CountryVocType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\ModalButtonType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SiteType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $site = $builder->getData();
    $builder
      ->add('codeStation', EntityCodeType::class, [
        "disabled" => $this->canEditAdminOnly($options),
      ])
      ->add('nomStation')
      ->add('infoDescription')
      ->add('countryFk', CountryVocType::class)
      ->add('municipalityFk', EntityType::class, array(
        'class' => 'App:Municipality',
        'query_builder' => function (EntityRepository $er) use ($site) {
          $query = $er->createQueryBuilder('municipality')
            ->orderBy('municipality.codeCommune', 'ASC');
          if ($site->getCountryFk()) {
            $query = $query->where('municipality.countryFk = :country')
              ->setParameter('country', $site->getCountryFk()->getId());
          }
          return $query;
        },
        'choice_label' => 'codeCommune',
        'multiple' => false,
        'expanded' => false,
        'placeholder' => 'Choose a Municipality',
      ))
      ->add('newMunicipality', ModalButtonType::class, [
        'label' => 'button.NewCommune',
        'icon_class' => 'fa-plus-circle',
        'attr' => [
          'class' => "btn-info btn-sm",
          "data-modal-controller" => 'App\\Controller\\Core\\MunicipalityController::newmodalAction',
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
      ->add('latDegDec', NumberType::class, array(
        'required' => true,
        'html5' => true,
        'scale' => 5,
        'attr' => array(
          'min' => -90,
          'max' => 90,
          'step' => 0.00001,
        ),
      ))
      ->add('longDegDec', NumberType::class, array(
        'required' => true,
        'html5' => true,
        'scale' => 5,
        'attr' => array(
          'min' => -180,
          'max' => 180,
          'step' => 0.00001,
        ),
      ))
      ->add('showNearbySites', ModalButtonType::class, [
        'label' => 'button.showNearbySites',
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
      ->add('altitudeM')
      ->add('comment');

    $this->upperCaseFields($builder, [
      'codeStation', 'nomStation',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Site',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'site';
  }
}
