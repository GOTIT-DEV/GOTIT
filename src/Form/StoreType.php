<?php

namespace App\Form;

use App\Form\EmbedTypes\DnaEmbedType;
use App\Form\EmbedTypes\InternalLotEmbedType;
use App\Form\EmbedTypes\SlideEmbedType;
use App\Form\Enums\Action;
use App\Form\Type\BaseVocType;
use App\Form\Type\EntityCodeType;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StoreType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {

    $storeType = $builder->getData()->getStorageTypeVocFk();

    $builder
    # Is not auto-generated : editable in create mode
    ->add('code', EntityCodeType::class, [
      'disabled' => $this->canEditAdminOnly($options),
    ])
      ->add('label')
      ->add('comment')
      ->add('collectionTypeVocFk', BaseVocType::class, array(
        'voc_parent' => 'typeCollection',
        'placeholder' => 'Choose a typeCollection',
      ))
      ->add('collectionCodeVocFk', BaseVocType::class, array(
        'voc_parent' => 'codeCollection',
        'placeholder' => 'Choose a Collection',
      ))
      ->add('storageTypeVocFk', BaseVocType::class, array(
        'voc_parent' => 'typeBoite',
        'placeholder' => 'Choose a typeBoite',
        'choice_label' => 'code',
        'disabled' => ($storeType != null),
      ));

    if ($storeType != null and $options["action_type"] != Action::create()) {
      switch ($storeType->getCode()) {
      case 'LOT':
        $builder->add('internalLots', CollectionType::class, array(
          'entry_type' => InternalLotEmbedType::class,
          // 'allow_add' => true,
          // 'allow_delete' => true,
          'prototype' => true,
          'prototype_name' => '__name__',
          'by_reference' => false,
          'required' => false,
          'entry_options' => array('label' => false),
          'disabled' => true,
        ));
        break;

      case 'ADN':
        $builder->add('dnas', CollectionType::class, array(
          'entry_type' => DnaEmbedType::class,
          // 'allow_add' => true,
          // 'allow_delete' => true,
          'prototype' => true,
          'prototype_name' => '__name__',
          'by_reference' => false,
          'required' => false,
          'entry_options' => array('label' => false),
          'disabled' => true,
        ));
        break;

      case 'LAME':
        $builder->add('slides', CollectionType::class, array(
          'entry_type' => SlideEmbedType::class,
          // 'allow_add' => true,
          // 'allow_delete' => true,
          'prototype' => true,
          'prototype_name' => '__name__',
          'by_reference' => false,
          'required' => false,
          'entry_options' => array('label' => false),
          'disabled' => true,
        ));
        break;
      default:
        throw new InvalidArgumentException("Unknown store type : " . $storeType->getCode());
        break;
      }
    }

  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Store',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'store';
  }
}
