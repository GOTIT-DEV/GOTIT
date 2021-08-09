<?php

namespace App\Form\EmbedTypes;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonEmbedType extends AbstractType {

  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults([
      'class' => 'App:Person',
      'query_builder' => function (EntityRepository $er) {
        return $er->createQueryBuilder('person')
          ->orderBy('person.nomPersonne', 'ASC');
      },
      'choice_label' => 'nom_personne',
      'multiple' => false,
      'expanded' => false,
      'label' => false,
      'placeholder' => 'Choose a Person',
    ]);
  }

  public function getParent() {
    return EntityType::class;
  }
}
