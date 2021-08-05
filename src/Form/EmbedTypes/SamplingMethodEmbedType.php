<?php

namespace App\Form\EmbedTypes;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use App\Form\Type\BaseVocType;

class SamplingMethodEmbedType extends AbstractType {
  /**
   * @param FormBuilderInterface $builder
   * @param array $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('samplingMethodVocFk', BaseVocType::class, [
      'voc_parent' => 'samplingMethod',
      'placeholder' => 'Choose a Sampling method',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\SamplingMethod',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'sampling_method_embed';
  }
}
