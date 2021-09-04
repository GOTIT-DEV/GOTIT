<?php

namespace App\Repository\Exception;
use Symfony\Component\Validator\ConstraintViolation;

class ConstraintViolationException extends Exception {

  protected $violation;

  public function __construct($message, $entity, $propertyPath, $invalidValue) {
    $this->violation = new ConstraintViolation($message, null, [], $entity, $propertyPath, $invalidValue);
    parent::__construct($message, $code, $previous);
  }

  public function getConstraintViolation() {
    return $this->violation;
  }
}