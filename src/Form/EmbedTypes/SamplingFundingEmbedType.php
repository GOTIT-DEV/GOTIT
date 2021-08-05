<?php

namespace App\Form\EmbedTypes;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class SamplingFundingEmbedType extends AbstractType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('programmeFk', EntityType::class, array(
      'class' => 'App:Programme',
      'query_builder' => function (EntityRepository $er) {
        return $er->createQueryBuilder('programme')
          ->orderBy('programme.codeProgramme', 'ASC');
      },
      'choice_label' => 'code_programme',
      'multiple' => false,
      'expanded' => false,
      'label' => false,
      'placeholder' => 'Choose a Program',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\SamplingFunding',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'sampling_funding';
  }
}
