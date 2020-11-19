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
use App\Form\Type\SearchableSelectType;
use App\Form\Type\GeneType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\BaseVocType;
use App\Form\Enums\Action;
use App\Form\EmbedTypes\PcrEstRealiseParEmbedType;
use App\Form\ActionFormType;

class PcrType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $adn = $builder->getData()->getAdnFk();
    $builder
      ->add('adnFk', SearchableSelectType::class, [
        'class' => 'App:Adn',
        'choice_label' => 'codeAdn',
        'placeholder' => $this->translator->trans("Adn typeahead placeholder"),
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $adn != null,
        ],
      ])
      ->add('codePcr', null, [
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $options['action_type'] == Action::create(),
        ],
      ])
      ->add('numPcr', null, [
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('geneVocFk', GeneType::class)
      ->add('primerPcrStartVocFk', BaseVocType::class, array(
        'voc_parent' => 'primerPcrStart',
        'placeholder' => 'Choose a primer start',
        'disabled' => $this->canEditAdminOnly($options),
      ))
      ->add('primerPcrEndVocFk', BaseVocType::class, array(
        'voc_parent' => 'primerPcrEnd',
        'placeholder' => 'Choose a primer end',
        'disabled' => $this->canEditAdminOnly($options),
      ))
      ->add('datePrecisionVocFk', DatePrecisionType::class)
      ->add('datePcr', DateFormattedType::class)
      ->add('qualitePcrVocFk', BaseVocType::class, array(
        'voc_parent' => 'qualitePcr',
        'placeholder' => 'Choose a quality',
      ))
      ->add('specificiteVocFk', BaseVocType::class, array(
        'voc_parent' => 'specificite',
        'placeholder' => 'Choose a specificity',
      ))
      ->add('detailPcr')
      ->add('remarquePcr')
      ->add('pcrEstRealisePars', CollectionType::class, array(
        'entry_type' => PcrEstRealiseParEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array('label' => false),
        'attr' => [
          "data-allow-new" => true,
          "data-modal-controller" =>
          'App\\Controller\\Core\\PersonneController::newmodalAction',
        ],
      ))
      ->addEventSubscriber($this->addUserDate);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Pcr',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'bbees_e3sbundle_pcr';
  }
}
