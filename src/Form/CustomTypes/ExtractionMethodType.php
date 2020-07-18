<?php

namespace App\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;


class ExtractionMethodType extends AbstractType
{


  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'class' => 'App:Voc',
      'query_builder' => function (EntityRepository $er) {
        return $er->createQueryBuilder('voc')
          ->where('voc.parent LIKE :parent')
          ->setParameter('parent', 'methodeExtractionAdn')
          ->orderBy('voc.libelle', 'ASC');
      },
      'choice_translation_domain' => true,
      'choice_label' => 'libelle',
      'multiple' => false,
      'expanded' => false,
      'placeholder' => 'Choose a method'
    ]);
  }

  public function getParent()
  {
    return EntityType::class;
  }
}
