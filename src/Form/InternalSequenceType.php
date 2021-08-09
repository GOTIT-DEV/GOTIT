<?php

namespace App\Form;

use App\Form\EmbedTypes\InternalSequenceAssemblerEmbedType;
use App\Form\EmbedTypes\InternalSequenceAssemblyEmbedType;
use App\Form\EmbedTypes\InternalSequencePublicationEmbedType;
use App\Form\EmbedTypes\TaxonIdentificationEmbedType;
use App\Form\Enums\Action;
use App\Form\Type\BaseVocType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\EntityCodeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InternalSequenceType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $sequence = $builder->getData();
    $gene = $options['gene'] ?: $sequence->getGeneVocFk();
    $specimen = $options['specimen'] ?: $sequence->getSpecimenFk();

    $builder
      ->add('codeSqcAss', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => ['readonly' => Action::create() == $options['action_type']],
      ])
      ->add('accessionNumber')
      ->add('codeSqcAlignement', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => Action::create() == $options['action_type'],
          'placeholder' => $this->translator->trans('Auto generated code'),
        ],
      ])
      ->add('commentaireSqcAss')
      ->add('datePrecisionVocFk', DatePrecisionType::class)
      ->add('dateCreationSqcAss', DateFormattedType::class)
      ->add('statutSqcAssVocFk', BaseVocType::class, [
        'voc_parent' => 'statutSqcAss',
        'choice_label' => 'code',
        'placeholder' => 'Choose a statut',
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('assemblies', CollectionType::class, array(
        'disabled' => $this->canEditAdminOnly($options),
        'entry_type' => InternalSequenceAssemblyEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array(
          'label' => false,
          'geneVocFk' => $gene,
          'specimenFk' => $specimen,
        ),
      ))
      ->add('assemblers', CollectionType::class, array(
        'entry_type' => InternalSequenceAssemblerEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array('label' => false),
        'attr' => [
          "data-allow-new" => true,
          "data-modal-controller" =>
          'App\\Controller\\Core\\PersonController::newmodalAction',
        ],
      ))
      ->add('taxonIdentifications', CollectionType::class, array(
        'entry_type' => TaxonIdentificationEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array('label' => false),
      ))
      ->add('publications', CollectionType::class, array(
        'entry_type' => InternalSequencePublicationEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'required' => false,
        'entry_options' => array('label' => false),
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
