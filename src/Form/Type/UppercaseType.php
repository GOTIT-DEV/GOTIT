<?php

namespace App\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use App\Form\DataTransformer\UppercaseTransformer;

class UppercaseType extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->addModelTransformer(new UppercaseTransformer());
  }

  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setNormalizer("attr", function (Options $options, $value) {
      $opts = $value;
      $opts['class'] = array_key_exists("class", $value)
      ? $value['class'] . " text-uppercase"
      : 'text-uppercase';
      return $opts;
    });
  }

  public function getParent() {
    return TextType::class;
  }
}
