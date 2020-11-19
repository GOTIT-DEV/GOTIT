<?php

/*
 * This file is part of the E3sBundle.
 *
 * Authors : see information concerning authors of GOTIT project in file AUTHORS.md
 *
 * E3sBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * E3sBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with E3sBundle.  If not, see <https://www.gnu.org/licenses/>
 *
 */

namespace App\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use App\Form\Type\BaseVocType;
use App\Form\Enums\Action;
use App\Form\EmbedTypes\LotMaterielEmbedType;
use App\Form\EmbedTypes\IndividuLameEmbedType;
use App\Form\EmbedTypes\AdnEmbedType;

class BoiteType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {

    $boxType = $builder->getData()->getTypeBoiteVocFk();

    $builder
    # Is not auto-generated : editable in create mode
    ->add('codeBoite', null, [
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
          'entry_type' => AdnEmbedType::class,
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
        $builder->add('individuLames', CollectionType::class, array(
          'entry_type' => IndividuLameEmbedType::class,
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

    $builder->addEventSubscriber($this->addUserDate);
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
