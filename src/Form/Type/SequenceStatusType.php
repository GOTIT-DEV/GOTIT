<?php

namespace App\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use App\Entity\Voc;

class SequenceStatusType extends AbstractType {

  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults([
      'class' => Voc::class,
      'query_builder' => function (EntityRepository $er) {
        return $er->createQueryBuilder('voc')
          ->where('voc.parent LIKE :parent')
          ->setParameter('parent', 'statutSqcAss')
          ->orderBy('voc.libelle', 'ASC');
      },
      'choice_label' => 'code',
      'multiple' => false,
      'expanded' => false,
      'placeholder' => 'Choose a statut',
    ]);
  }

  public function getParent(): ?string {
    return EntityType::class;
  }
}
