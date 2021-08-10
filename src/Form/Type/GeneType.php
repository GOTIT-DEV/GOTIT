<?php

namespace App\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GeneType extends AbstractType {

  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults([
      'class' => 'App:Voc',
      'query_builder' => function (EntityRepository $er) {
        return $er->createQueryBuilder('voc')
          ->where("voc.parent = 'gene'")
          ->orderBy('voc.label', 'ASC');
      },
      'choice_label' => 'label',
      'multiple' => false,
      'expanded' => false,
      'placeholder' => 'Choose a gene',
    ]);
  }

  public function getParent() {
    return EntityType::class;
  }
}
