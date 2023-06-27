<?php

namespace App\Form\EmbedTypes;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use App\Entity\Personne;

class PersonneEmbedType extends AbstractType {

  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults([
      'class' => Personne::class,
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

  public function getParent(): ?string {
    return EntityType::class;
  }
}
