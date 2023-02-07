<?php

namespace App\Form\EmbedTypes;

use App\Form\EmbedTypes\PersonneEmbedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EstEffectueParEmbedType extends AbstractType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('personneFk', PersonneEmbedType::class);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\EstEffectuePar',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix():string {
    return 'bbees_e3sbundle_esteffectuepar';
  }
}
