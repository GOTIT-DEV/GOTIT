<?php

namespace App\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DynamicCollectionType extends CollectionType {

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'allow_add' => true,
      'allow_delete' => true,
      'prototype' => true,
      'prototype_name' => '__name__',
      'by_reference' => false,
      'entry_options' => ['label' => false],
    ));
  }
}
