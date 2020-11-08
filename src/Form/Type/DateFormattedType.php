<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class DateFormattedType extends AbstractType
{


  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'widget' => 'single_text',
      'format' => 'dd-MM-yyyy',
      'required' => false,
    ]);
  }

  public function getParent()
  {
    return DateType::class;
  }
}