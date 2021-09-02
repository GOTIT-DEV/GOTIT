<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\EmbedTypes\DnaProducerEmbedType;
use App\Form\Type\BaseVocType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\SearchableSelectType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DnaType extends ActionFormType {

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $specimen = $builder->getData()->getSpecimenFk();
    $builder
      ->add('specimenFk', SearchableSelectType::class, [
        'class' => 'App:Specimen',
        'choice_label' => 'molecularCode',
        'placeholder' => $this->translator->trans("Specimen typeahead placeholder"),
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $specimen != null,
        ],
      ])
      # Is not auto-generated : editable in create mode
      ->add('code', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('datePrecisionVocFk', DatePrecisionType::class)
      ->add('date', DateFormattedType::class)
      ->add('extractionMethodVocFk', BaseVocType::class, [
        'voc_parent' => "methodeExtractionAdn",
        'placeholder' => 'Choose a method',
      ])
      ->add('concentrationNgMicrolitre', NumberType::class, array(
        'scale' => 4,
        'required' => false,
      ))
      ->add('comment')
      ->add('qualiteAdnVocFk', BaseVocType::class, [
        'voc_parent' => 'qualiteAdn',
        'placeholder' => 'Choose a quality',
      ])
      ->add('storeFk', EntityType::class, array(
        'class' => 'App:Store',
        'query_builder' => function (EntityRepository $er) {
          return $er->createQueryBuilder('store')
            ->leftJoin('App:Voc', 'voc', 'WITH', 'store.storageTypeVocFk = voc.id')
            ->where('voc.code LIKE :codetype')
            ->setParameter('codetype', 'ADN')
            ->orderBy('LOWER(store.code)', 'ASC');
        },
        'placeholder' => 'Choose a Box',
        'choice_label' => 'code',
        'multiple' => false,
        'expanded' => false,
        'required' => false,
      ))
      ->add('dnaProducers', CollectionType::class, [
        'entry_type' => DnaProducerEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array('label' => false),
        'attr' => [
          "data-allow-new" => true,
          "data-modal-controller" => 'App\\Controller\\Core\\PersonController::newmodalAction',
        ],
      ]);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Dna',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'dna';
  }
}
