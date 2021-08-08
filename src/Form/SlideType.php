<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\EmbedTypes\SlidePreparationEmbedType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\SearchableSelectType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SlideType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $specimen = $builder->getData()->getSpecimenFk();

    $builder
      ->add('specimenFk', SearchableSelectType::class, [
        'class' => 'App:Specimen',
        'choice_label' => 'codeIndTriMorpho',
        'placeholder' => $this->translator
          ->trans("Specimen codeIndTriMorpho typeahead placeholder"),
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $specimen != null,
        ],
      ])
      ->add('codeLameColl', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('libelleLame')
      ->add('datePrecisionVocFk', DatePrecisionType::class)
      ->add('dateLame', DateFormattedType::class)
      ->add('nomDossierPhotos')
      ->add('commentaireLame')
      ->add('boiteFk', EntityType::class, array(
        'class' => 'App:Boite',
        'query_builder' => function (EntityRepository $er) {
          return $er->createQueryBuilder('boite')
            ->leftJoin('App:Voc', 'voc', 'WITH', 'boite.typeBoiteVocFk = voc.id')
            ->where('voc.code LIKE :codetype')
            ->setParameter('codetype', 'LAME')
            ->orderBy('LOWER(boite.codeBoite)', 'ASC');
        },
        'placeholder' => 'Choose a Box',
        'choice_label' => 'codeBoite',
        'multiple' => false,
        'expanded' => false,
        'required' => false,
      ))
      ->add('slidePreparations', CollectionType::class, array(
        'entry_type' => SlidePreparationEmbedType::class,
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
      ));
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Slide',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'slide';
  }
}
