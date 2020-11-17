<?php

namespace App\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class TaxnameType extends AbstractType {

  public function configureOptions(OptionsResolver $resolver) {
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
      'placeholder' => 'Choose a Taxon',
      'choice_attr' => function ($choice, $key, $value) {
        return ['data-code' => $choice->getCodeTaxon()];
      },
    ]);
  }

  public function getParent() {
    return EntityType::class;
  }
}
