<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\EmbedTypes\SamplingFixativeEmbedType;
use App\Form\EmbedTypes\SamplingFundingEmbedType;
use App\Form\EmbedTypes\SamplingMethodEmbedType;
use App\Form\EmbedTypes\SamplingParticipantEmbedType;
use App\Form\EmbedTypes\TaxonSamplingEmbedType;
use App\Form\Enums\Action;
use App\Form\Type\BaseVocType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\SearchableSelectType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SamplingType extends ActionFormType {

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $sampling = $builder->getData();
    $builder
      ->add('siteFk', SearchableSelectType::class, [
        'class' => 'App:Site',
        'choice_label' => 'codeStation',
        'placeholder' =>
        $this->translator->trans("Site typeahead placeholder"),
        'attr' => [
          "maxlength" => "255",
          'readonly' => ($options['action_type'] == Action::create() &&
            $sampling->getSiteFk()),
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
      ->add('samplingMethods', CollectionType::class, [
        'entry_type' => SamplingMethodEmbedType::class,
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
      ->add('samplingFundings', CollectionType::class, [
        'entry_type' => SamplingFundingEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'attr' => [
          "data-allow-new" => true,
          "data-modal-controller" =>
          'App\\Controller\\Core\\ProgramController::newmodalAction',
        ],
        'entry_options' => array('label' => false),
      ])
      ->add('samplingParticipants', CollectionType::class, [
        'entry_type' => SamplingParticipantEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'attr' => [
          "data-allow-new" => true,
          "data-modal-controller" =>
          'App\\Controller\\Core\\PersonController::newmodalAction',
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
      'data_class' => 'App\Entity\Sampling',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'sampling';
  }
}