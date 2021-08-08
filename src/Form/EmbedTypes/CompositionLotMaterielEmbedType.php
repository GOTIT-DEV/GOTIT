<?php

namespace App\Form\EmbedTypes;

use App\Form\Type\BaseVocType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompositionLotMaterielEmbedType extends AbstractType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('specimentCount')
      ->add('specimenTypeVocFk', BaseVocType::class, [
        'voc_parent' => 'typeIndividu',
        'placeholder' => 'Choose a Type',
      ])
      ->add('commentaireCompoLotMateriel');
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\CompositionLotMateriel',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'bbees_e3sbundle_compositionlotmateriel';
  }
}
