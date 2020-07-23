<?php

namespace App\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class TaxnameType extends AbstractType
{


  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'class' => 'App:ReferentielTaxon',
      'query_builder' => function (EntityRepository $er) {
        return $er->createQueryBuilder('rt')
          ->orderBy('rt.taxname', 'ASC');
      },
      'choice_label' => 'taxname',
      'multiple' => false,
      'expanded' => false,
      'required' => true,
      'placeholder' => 'Choose a Taxon'
    ]);
  }

  public function getParent()
  {
    return EntityType::class;
  }
}
