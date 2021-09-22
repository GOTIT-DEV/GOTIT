<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\EmbedTypes\PersonEmbedType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DynamicCollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MotuDatasetType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder
      ->add('filename')
      ->add('title')
      ->add('date', DateFormattedType::class, ['required' => true])
      ->add('comment')
      ->add('motuDelimiters', DynamicCollectionType::class, array(
        'entry_type' => PersonEmbedType::class,
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
