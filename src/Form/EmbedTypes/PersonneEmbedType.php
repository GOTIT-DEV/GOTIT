<?php

namespace App\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class PersonneEmbedType extends AbstractType
{


  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'class' => 'App:Personne',
      'query_builder' => function (EntityRepository $er) {
        return $er->createQueryBuilder('personne')
          ->orderBy('personne.nomPersonne', 'ASC');
      },
      'choice_label' => 'nom_personne',
      'multiple' => false,
      'expanded' => false,
      'label' => false,
      'placeholder' => 'Choose a Person',
    ]);
  }

  public function getParent()
  {
    return EntityType::class;
  }
}
