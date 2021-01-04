<?php

namespace App\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;

class EntityCodeType extends AbstractType {

  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setNormalizer("attr", function (Options $options, $value) {
      $value['class'] = "text-monospace " . ($value['class'] ?? "");
      return $value;
    });
  }

  public function getParent() {
    return TextType::class;
  }
}
