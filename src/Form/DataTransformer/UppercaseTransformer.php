<?php

namespace App\Form\DataTransformer;


use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class UppercaseTransformer implements DataTransformerInterface
{
  public function transform($text){
    return $text;
  }

  public function reverseTransform($text)
  {
    return strtoupper($text);
  }
}