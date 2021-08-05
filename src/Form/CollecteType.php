<?php

namespace App\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Form\Type\SearchableSelectType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\BaseVocType;
use App\Form\Enums\Action;
use App\Form\EmbedTypes\TaxonSamplingEmbedType;
use App\Form\EmbedTypes\SamplingFixativeEmbedType;
use App\Form\EmbedTypes\EstFinanceParEmbedType;
use App\Form\EmbedTypes\EstEffectueParEmbedType;
use App\Form\EmbedTypes\APourSamplingMethodEmbedType;
use App\Form\ActionFormType;

class CollecteType extends ActionFormType {

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $sampling = $builder->getData();
    $builder
      ->add('stationFk', SearchableSelectType::class, [
        'class' => 'App:Station',
        'choice_label' => 'codeStation',
        'placeholder' =>
        $this->translator->trans("Station typeahead placeholder"),
        'attr' => [
          "maxlength" => "255",
          'readonly' => ($options['action_type'] == Action::create() &&
            $sampling->getStationFk()),
        ],
      ])
      ->add('codeCollecte', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'class' => 'sampling-code',
          'readonly' => $options['action_type'] == Action::create(),
        ],
      ])
      ->add('datePrecisionVocFk', DatePrecisionType::class, [
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('dateCollecte', DateFormattedType::class, [
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('aPourSamplingMethods', CollectionType::class, [
        'entry_type' => APourSamplingMethodEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => [
          "label" => false,
        ],
      ])
      ->add('samplingFixatives', CollectionType::class, [
        'entry_type' => SamplingFixativeEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => ['label' => false],
      ])
      ->add('estFinancePars', CollectionType::class, [
        'entry_type' => EstFinanceParEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'attr' => [
          "data-allow-new" => true,
          "data-modal-controller" =>
          'App\\Controller\\Core\\ProgrammeController::newmodalAction',
        ],
        'entry_options' => array('label' => false),
      ])
      ->add('estEffectuePars', CollectionType::class, [
        'entry_type' => EstEffectueParEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'attr' => [
          "data-allow-new" => true,
          "data-modal-controller" =>
          'App\\Controller\\Core\\PersonneController::newmodalAction',
        ],
        'entry_options' => array('label' => false),
      ])
      ->add('taxonSamplings', CollectionType::class, [
        'entry_type' => TaxonSamplingEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array('label' => false),
      ])
      ->add('dureeEchantillonnageMn', IntegerType::class, [
        'attr' => ["min" => "0"],
        'required' => false,
      ])
      ->add('temperatureC')
      ->add('conductiviteMicroSieCm', IntegerType::class, [
        'attr' => ["min" => "0"],
        'required' => false,
      ])
      ->add('aFaire', ChoiceType::class, [
        'choices' => ['YES' => 1, 'NO' => 0],
        'required' => true,
        'choice_translation_domain' => true,
        'multiple' => false,
        'expanded' => true,
        'label_attr' => ['class' => 'radio-inline'],
      ])
      ->add('commentaireCollecte')
      ->add('legVocFk', BaseVocType::class, [
        'voc_parent' => 'leg',
        'sort_by_id' => true,
        'expanded' => true,
        'label_attr' => ['class' => 'radio-inline'],
      ]);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults([
      'data_class' => 'App\Entity\Collecte',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'bbees_e3sbundle_collecte';
  }
}
