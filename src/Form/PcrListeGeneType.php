<?php

namespace App\Form;

use App\Form\Type\GeneType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PcrListeGeneType extends AbstractType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('gene', GeneType::class);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults([
      'data_class' => 'App\Entity\Pcr',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'pcr';
  }
}
