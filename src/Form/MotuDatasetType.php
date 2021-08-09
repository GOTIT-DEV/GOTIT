<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\EmbedTypes\MotuDelimiterEmbedType;
use App\Form\Type\DateFormattedType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MotuDatasetType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder
      ->add('nomFichierCsv')
      ->add('libelleMotu')
      ->add('dateMotu', DateFormattedType::class, ['required' => true])
      ->add('commentaireMotu')
      ->add('motuDelimiters', CollectionType::class, array(
        'entry_type' => MotuDelimiterEmbedType::class,
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
      ));
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\MotuDataset',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'motu_dataset';
  }
}
