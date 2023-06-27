<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\SecurityBundle\Security;

class CoordinateType extends AbstractType {
  private $security;

  public function __construct(
    Security $security
  ) {
    $this->security = $security;
  }
  public function configureOptions(OptionsResolver $resolver): void {
    $user = $this->security->getUser();
    $resolver->setNormalizer('scale', static function (Options $opts, $value) use ($user) {
      return $user instanceof User ? 5 : 2;
    });
  }
  public function getParent(): ?string {
    return NumberType::class;
  }
}
