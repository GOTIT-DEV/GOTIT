<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\EmbedTypes\ExternalLotProducerEmbedType;
use App\Form\EmbedTypes\SourceEmbedType;
use App\Form\EmbedTypes\TaxonIdentificationEmbedType;
use App\Form\Enums\Action;
use App\Form\Type\BaseVocType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\DynamicCollectionType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\SearchableSelectType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExternalLotType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $sampling = $builder->getData()->getSampling();

    $builder
      ->add('sampling', SearchableSelectType::class, [
        'class' => 'App:Sampling',
        'choice_label' => 'code',
        'placeholder' => $this->translator->trans("Sampling typeahead placeholder"),
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $sampling != null,
        ],
      ])
      ->add('code', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
        "attr" => [
          'readonly' => ($options['action_type'] == Action::create()),
        ],
      ])
      ->add('pigmentation', BaseVocType::class, [
        'voc_parent' => 'pigmentation',
        'placeholder' => 'Choose a Pigmentation',
      ])
      ->add('eyes', BaseVocType::class, [
        'voc_parent' => 'yeux',
        'placeholder' => 'Choose a Eye',
      ])
      ->add('comment')
      ->add('specimenQuantity', BaseVocType::class, [
        'voc_parent' => 'nbIndividus',
        'placeholder' => 'Choose an option',
      ])
      ->add('specimenQuantityComment')
      ->add('datePrecision', DatePrecisionType::class)
      ->add('creationDate', DateFormattedType::class)
      ->add('producers', DynamicCollectionType::class, [
        'entry_type' => ExternalLotProducerEmbedType::class,
        'attr' => [
          "data-allow-new" => true,
          "data-modal-controller" => 'App\\Controller\\Core\\PersonController::newmodalAction',
        ],
      ])
      ->add('taxonIdentifications', DynamicCollectionType::class, [
        'entry_type' => TaxonIdentificationEmbedType::class,
      ])
      ->add('publications', DynamicCollectionType::class, [
        'entry_type' => SourceEmbedType::class,
      ]);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(['data_class' => 'App\Entity\ExternalLot']);
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'external_lot';
  }
}
