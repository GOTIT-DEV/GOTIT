<?php

namespace App\Form;

use App\Form\EmbedTypes\PersonEmbedType;
use App\Form\EmbedTypes\SourceEmbedType;
use App\Form\EmbedTypes\TaxonIdentificationEmbedType;
use App\Form\Enums\Action;
use App\Form\Type\BaseVocType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\DynamicCollectionType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\GeneType;
use App\Form\Type\SearchableSelectType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExternalSequenceType extends ActionFormType {
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
        'attr' => [
          'readonly' => Action::create() == $options['action_type'],
        ],
      ])
      ->add('alignmentCode', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => Action::create() == $options['action_type'],
        ],
      ])
      ->add('accessionNumber', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('specimenMolecularNumber', null, [
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('primaryTaxon')
      ->add('originVocFk', BaseVocType::class, [
        'voc_parent' => 'origineSqcAssExt',
        'choice_label' => 'code',
        'placeholder' => 'Choose a origineSqcAssExt',
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('gene', GeneType::class)
      ->add('status', BaseVocType::class, [
        'voc_parent' => 'statutSqcAss',
        'choice_label' => 'code',
        'placeholder' => 'Choose a statut',
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('datePrecision', DatePrecisionType::class)
      ->add('dateCreation', DateFormattedType::class)
      ->add('comment')
      ->add('assemblers', DynamicCollectionType::class, array(
        'entry_type' => PersonEmbedType::class,
        'attr' => [
          "data-allow-new" => true,
          "data-modal-controller" => 'App\\Controller\\Core\\PersonController::newmodalAction',
        ],
      ))
      ->add('taxonIdentifications', DynamicCollectionType::class, array(
        'entry_type' => TaxonIdentificationEmbedType::class,
        'entry_options' => array(
          'label' => false,
          'refTaxonLabel' => $options['refTaxonLabel'],
        ),
      ))
      ->add('publications', DynamicCollectionType::class, array(
        'entry_type' => SourceEmbedType::class,
      ));
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\ExternalSequence',
      'refTaxonLabel' => 'taxname',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'external_sequence';
  }
}
