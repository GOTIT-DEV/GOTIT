<?php

namespace App\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class GeneType extends AbstractType {

  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults([
      'class' => 'App:Voc',
      'query_builder' => function (EntityRepository $er) {
        return $er->createQueryBuilder('voc')
          ->where("voc.parent = 'gene'")
          ->orderBy('voc.libelle', 'ASC');
      },
      'choice_label' => 'libelle',
      'multiple' => false,
      'expanded' => false,
      'placeholder' => 'Choose a gene',
    ]);
  }

  public function getParent() {
    return EntityType::class;
  }
}
