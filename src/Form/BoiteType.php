<?php

namespace App\Form;

use App\Form\EmbedTypes\DnaEmbedType;
use App\Form\EmbedTypes\LotMaterielEmbedType;
use App\Form\EmbedTypes\SlideEmbedType;
use App\Form\Enums\Action;
use App\Form\Type\BaseVocType;
use App\Form\Type\EntityCodeType;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BoiteType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {

    $boxType = $builder->getData()->getTypeBoiteVocFk();

    $builder
    # Is not auto-generated : editable in create mode
    ->add('codeBoite', EntityCodeType::class, [
      'disabled' => $this->canEditAdminOnly($options),
    ])
      ->add('libelleBoite')
      ->add('commentaireBoite')
      ->add('typeCollectionVocFk', BaseVocType::class, array(
        'voc_parent' => 'typeCollection',
        'placeholder' => 'Choose a typeCollection',
      ))
      ->add('codeCollectionVocFk', BaseVocType::class, array(
        'voc_parent' => 'codeCollection',
        'placeholder' => 'Choose a Collection',
      ))
      ->add('typeBoiteVocFk', BaseVocType::class, array(
        'voc_parent' => 'typeBoite',
        'placeholder' => 'Choose a typeBoite',
        'choice_label' => 'code',
        'disabled' => ($boxType != null),
      ));

    if ($boxType != null and $options["action_type"] != Action::create()) {
      switch ($boxType->getCode()) {
      case 'LOT':
        $builder->add('lotMateriels', CollectionType::class, array(
          'entry_type' => LotMaterielEmbedType::class,
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
        $builder->add('adns', CollectionType::class, array(
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
        throw new InvalidArgumentException("Unknown box type : " . $boxType->getCode());
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
      'data_class' => 'App\Entity\Boite',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'bbees_e3sbundle_boite';
  }
}
