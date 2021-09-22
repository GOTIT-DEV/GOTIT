<?php

namespace App\Form\EmbedTypes;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SourceEmbedType extends AbstractType {
  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults(array(
      'class' => 'App\Entity\Source',
      'query_builder' => function (EntityRepository $er) {
        return $er->createQueryBuilder('source')
          ->orderBy('source.code', 'ASC');
      },
      'placeholder' => 'Choose a Source',
      'choice_label' => 'code',
      'multiple' => false,
      'expanded' => false,
      'label' => false,
    ));
  }

  public function getParent() {
    return EntityType::class;
  }
}
