<?php

namespace App\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class CountryVocType extends AbstractType {

  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults([
      'class' => 'App:Country',
      'query_builder' => function (EntityRepository $er) {
        return $er->createQueryBuilder('country')
          ->orderBy('country.nomPays', 'ASC');
      },
      'placeholder' => 'Choose a Country',
      'choice_label' => 'nom_pays',
      'multiple' => false,
      'expanded' => false,
    ]);
  }

  public function getParent() {
    return EntityType::class;
  }
}
