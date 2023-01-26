<?php

namespace App\Form\EmbedTypes;

use App\Form\Type\BaseVocType;
use App\Form\UserDateTraceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompositionLotMaterielEmbedType extends UserDateTraceType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('nbIndividus')
      ->add('typeIndividuVocFk', BaseVocType::class, [
        'voc_parent' => 'typeIndividu',
        'placeholder' => 'Choose a Type',
      ])
      ->add('commentaireCompoLotMateriel')
      ->addEventSubscriber($this->addUserDate);
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
  public function getBlockPrefix():string {
    return 'bbees_e3sbundle_compositionlotmateriel';
  }
}
