<?php

namespace App\Form\EmbedTypes;

use App\Form\EmbedTypes\PersonEmbedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExternalLotProducerEmbedType extends AbstractType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('personFk', PersonEmbedType::class);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\ExternalLotProducer',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'producer';
  }
}
