<?php

namespace App\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;

class DateFormattedType extends AbstractType {

  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults([
      'widget' => 'single_text',
      'format' => 'dd-MM-yyyy',
      'required' => false,
      'html5' => false,
    ]);
  }

  public function getParent() {
    return DateType::class;
  }
}
