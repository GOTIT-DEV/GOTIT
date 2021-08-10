<?php

namespace App\Form\EmbedTypes;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SamplingFundingEmbedType extends AbstractType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('programFk', EntityType::class, array(
      'class' => 'App:Program',
      'query_builder' => function (EntityRepository $er) {
        return $er->createQueryBuilder('program')
          ->orderBy('program.code', 'ASC');
      },
      'choice_label' => 'code',
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
