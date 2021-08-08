<?php

namespace App\Form;

use App\Form\EmbedTypes\EspeceIdentifieeEmbedType;
use App\Form\EmbedTypes\EstAligneEtTraiteEmbedType;
use App\Form\EmbedTypes\SequenceAssembleeEstRealiseParEmbedType;
use App\Form\EmbedTypes\SequencePublicationEmbedType;
use App\Form\Enums\Action;
use App\Form\Type\BaseVocType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\EntityCodeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SequenceAssembleeType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $sequence = $builder->getData();
    $gene = $options['gene'] ?: $sequence->getGeneVocFk();
    $specimen = $options['specimen'] ?: $sequence->getIndividuFk();

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
      ->add('estAligneEtTraites', CollectionType::class, array(
        'disabled' => $this->canEditAdminOnly($options),
        'entry_type' => EstAligneEtTraiteEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array(
          'label' => false,
          'geneVocFk' => $gene,
          'individuFk' => $specimen,
        ),
      ))
      ->add('sequenceAssembleeEstRealisePars', CollectionType::class, array(
        'entry_type' => SequenceAssembleeEstRealiseParEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array('label' => false),
        'attr' => [
          "data-allow-new" => true,
          "data-modal-controller" =>
          'App\\Controller\\Core\\PersonneController::newmodalAction',
        ],
      ))
      ->add('especeIdentifiees', CollectionType::class, array(
        'entry_type' => EspeceIdentifieeEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array('label' => false),
      ))
      ->add('sequencePublications', CollectionType::class, array(
        'entry_type' => SequencePublicationEmbedType::class,
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
      'data_class' => 'App\Entity\SequenceAssemblee',
      'gene' => null,
      'specimen' => null,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'sequence_assemblee';
  }
}
