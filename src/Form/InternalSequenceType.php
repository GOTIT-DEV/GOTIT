<?php

namespace App\Form;

use App\Form\EmbedTypes\InternalSequenceAssemblyEmbedType;
use App\Form\EmbedTypes\PersonEmbedType;
use App\Form\EmbedTypes\SourceEmbedType;
use App\Form\EmbedTypes\TaxonIdentificationEmbedType;
use App\Form\Enums\Action;
use App\Form\Type\BaseVocType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\DynamicCollectionType;
use App\Form\Type\EntityCodeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InternalSequenceType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $sequence = $builder->getData();
    $gene = $options['gene'] ?: $sequence->getGene();
    $specimen = $options['specimen'] ?: $sequence->getSpecimen();

    $builder
      ->add('code', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => ['readonly' => Action::create() == $options['action_type']],
      ])
      ->add('accessionNumber')
      ->add('alignmentCode', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => Action::create() == $options['action_type'],
          'placeholder' => $this->translator->trans('Auto generated code'),
        ],
      ])
      ->add('comment')
      ->add('datePrecision', DatePrecisionType::class)
      ->add('creationDate', DateFormattedType::class)
      ->add('status', BaseVocType::class, [
        'voc_parent' => 'statutSqcAss',
        'choice_label' => 'code',
        'placeholder' => 'Choose a statut',
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('chromatograms', DynamicCollectionType::class, array(
        'disabled' => $this->canEditAdminOnly($options),
        'entry_type' => InternalSequenceAssemblyEmbedType::class,
        'entry_options' => array(
          'label' => false,
          'gene' => $gene,
          'specimen' => $specimen,
        ),
      ))
      ->add('assemblers', DynamicCollectionType::class, array(
        'entry_type' => PersonEmbedType::class,
        'attr' => [
          "data-allow-new" => true,
          "data-modal-controller" =>
          'App\\Controller\\Core\\PersonController::newmodalAction',
        ],
      ))
      ->add('taxonIdentifications', DynamicCollectionType::class, array(
        'entry_type' => TaxonIdentificationEmbedType::class,
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
    $resolver->setDefaults([
      'data_class' => 'App\Entity\InternalSequence',
      'gene' => null,
      'specimen' => null,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'internal_sequence';
  }
}
