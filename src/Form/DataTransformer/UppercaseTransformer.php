<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class UppercaseTransformer implements DataTransformerInterface {
  public function transform($text) {
    return $text;
  }

  public function reverseTransform($text) {
    return $text ? strtoupper($text) : null;
  }
}