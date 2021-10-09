<?php

namespace App\Entity\Abstraction;

/**
 * Entities having composite code generated from their properties
 */
trait CompositeCodeEntityTrait {
  /**
   * Generate composite code from entity properties
   */
  public function generateCode(): string {
    try {
      return $this->_generateCode();
    } catch (\Throwable) {
      throw new \InvalidArgumentException(
        sprintf(
          'Failed to generate code for %s entity. ',
          static::class
        ) . 'Please check that all required properties are set.',
      );
    }
  }

  public function updateCode(): void {
    $this->setCode($this->generateCode());
  }

  /**
   * Validates that actual code matches its specification
   */
  public function hasValidCode(): bool {
    return $this->getCode() === $this->generateCode();
  }
}
