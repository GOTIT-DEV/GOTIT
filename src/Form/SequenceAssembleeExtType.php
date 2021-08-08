<?php

namespace App\Form;

use App\Form\EmbedTypes\EspeceIdentifieeEmbedType;
use App\Form\EmbedTypes\ExternalSequencePublicationEmbedType;
use App\Form\EmbedTypes\SqcExtEstRealiseParEmbedType;
use App\Form\Enums\Action;
use App\Form\Type\BaseVocType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\GeneType;
use App\Form\Type\SearchableSelectType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SequenceAssembleeExtType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $sampling = $builder->getData()->getCollecteFk();
    $builder
      ->add('collecteFk', SearchableSelectType::class, [
        'class' => 'App:Collecte',
        'choice_label' => 'codeCollecte',
        'placeholder' => $this->translator->trans("Collecte typeahead placeholder"),
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $sampling != null,
        ],
      ])
      ->add('codeSqcAssExt', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => Action::create() == $options['action_type'],
        ],
      ])
      ->add('codeSqcAssExtAlignement', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => Action::create() == $options['action_type'],
        ],
      ])
      ->add('accessionNumberSqcAssExt', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('numIndividuSqcAssExt', null, [
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('taxonOrigineSqcAssExt')
      ->add('origineSqcAssExtVocFk', BaseVocType::class, [
        'voc_parent' => 'origineSqcAssExt',
        'choice_label' => 'code',
        'placeholder' => 'Choose a origineSqcAssExt',
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('geneVocFk', GeneType::class)
      ->add('statutSqcAssVocFk', BaseVocType::class, [
        'voc_parent' => 'statutSqcAss',
        'choice_label' => 'code',
        'placeholder' => 'Choose a statut',
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('datePrecisionVocFk', DatePrecisionType::class)
      ->add('dateCreationSqcAssExt', DateFormattedType::class)
      ->add('commentaireSqcAssExt')
      ->add('sqcExtEstRealisePars', CollectionType::class, array(
        'entry_type' => SqcExtEstRealiseParEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array('label' => false),
        'attr' => [
          "data-allow-new" => true,
          "data-modal-controller" => 'App\\Controller\\Core\\PersonneController::newmodalAction',
        ],
      ))
      ->add('especeIdentifiees', CollectionType::class, array(
        'entry_type' => EspeceIdentifieeEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array(
          'label' => false,
          'refTaxonLabel' => $options['refTaxonLabel'],
        ),
      ))
      ->add('externalSequencePublications', CollectionType::class, array(
        'entry_type' => ExternalSequencePublicationEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array('label' => false),
      ));
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\SequenceAssembleeExt',
      'refTaxonLabel' => 'taxname',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'bbees_e3sbundle_sequenceassembleeext';
  }
}
