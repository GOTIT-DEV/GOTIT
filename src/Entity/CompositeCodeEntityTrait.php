<?php

namespace App\Entity;

/**
 * Entities having composite code generated from their properties
 */
trait CompositeCodeEntityTrait {
  /**
   * Generate composite code from entity properties
   * @return string
   */
  public function generateCode(): string {
    try {
      return $this->_generateCode();
    } catch (\Throwable $e) {
      throw new \InvalidArgumentException(
        sprintf("Failed to generate code for %s entity. ", static::class) .
        "Please check that all required properties are set.");
    }
  }

  public function updateCode() {
    $this->setCode($this->generateCode());
  }

  /**
   * Validates that actual code matches its specification
   * @return bool
   */
  public function hasValidCode(): bool {
    return $this->getCode() === $this->generateCode();
  }
}
