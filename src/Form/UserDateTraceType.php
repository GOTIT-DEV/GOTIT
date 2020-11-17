<?php

namespace App\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\AbstractType;
use App\Form\EventListener\AddUserDateFields;

class UserDateTraceType extends AbstractType {
  protected $addUserDate;

  /**
   * {@inheritdoc}
   */
  public function __construct(AddUserDateFields $addUserDate) {
    $this->addUserDate = $addUserDate;
  }

  public function configureOptions(OptionsResolver $resolver) {
    $resolver->addNormalizer(
      'allow_extra_fields',
      function (Options $options, $value) {
        return true;
      }
    );
  }
}
