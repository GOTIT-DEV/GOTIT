<?php

namespace App\Form\EmbedTypes;

use App\Form\Type\BaseVocType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class APourSamplingMethodEmbedType extends AbstractType {
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
      'data_class' => 'App\Entity\APourSamplingMethod',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'bbees_e3sbundle_apoursamplingmethod_embed';
  }
}
