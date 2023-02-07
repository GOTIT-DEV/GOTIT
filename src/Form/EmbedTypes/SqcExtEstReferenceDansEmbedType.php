<?php

namespace App\Form\EmbedTypes;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SqcExtEstReferenceDansEmbedType extends AbstractType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('sourceFk', EntityType::class, array(
      'class' => 'App:Source',
      'query_builder' => function (EntityRepository $er) {
        return $er->createQueryBuilder('source')
          ->orderBy('source.codeSource', 'ASC');
      },
      'placeholder' => 'Choose a Source',
      'choice_label' =>
      'code_source',
      'multiple' => false,
      'expanded' => false,
      'label' => false,
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\SqcExtEstReferenceDans',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix():string {
    return 'bbees_e3sbundle_sqcextestreferencedans';
  }
}
