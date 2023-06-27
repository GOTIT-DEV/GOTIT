<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class UppercaseTransformer implements DataTransformerInterface {
  public function transform($text): mixed {
    return $text;
  }

  public function reverseTransform($text): mixed {
    return $text ? strtoupper($text) : null;
  }
}
