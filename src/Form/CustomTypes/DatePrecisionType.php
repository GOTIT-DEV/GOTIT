<?php

namespace App\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\Options;

class DatePrecisionType extends AbstractType
{


  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'class' => 'App\Entity\Voc',
      'query_builder' => function (EntityRepository $er) {
        return $er->createQueryBuilder('voc')
          ->where('voc.parent LIKE :parent')
          ->setParameter('parent', 'datePrecision')
          ->orderBy('voc.id', 'ASC');
      },
      'choice_translation_domain' => true,
      'choice_label' => 'libelle',
      'multiple' => false,
      'expanded' => true,
      'label_attr' => array('class' => 'radio-inline'),
      'required' => true,
      'readonly' => false
    ]);
    $resolver->setDefault('attr', function(Options $options){
      return [
        "class" => "date-precision",
        'readonly' => $options['readonly']
      ];
    });
  }

  public function getParent()
  {
    return EntityType::class;
  }
}
