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
use App\Form\Type\SpecimenVocType;
use App\Form\Type\SearchableSelectType;
use App\Form\Enums\Action;
use App\Form\EmbedTypes\EspeceIdentifieeEmbedType;
use App\Form\ActionFormType;

class IndividuType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {

    $hasBioMol = (bool) $builder->getData()->getCodeIndBiomol();
    $bioMat = $builder->getData()->getLotMaterielFk();

    $builder
      ->add('lotMaterielFk', SearchableSelectType::class, [
        'class' => 'App:LotMateriel',
        'choice_label' => 'codeLotMateriel',
        'placeholder' => $this->translator->trans("Lotmateriel typeahead placeholder"),
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $bioMat != null,
        ],
      ])
      ->add('codeTube', null, [
        'disabled' => $hasBioMol && $this->canEditAdminOnly($options),
      ])
      ->add('codeIndTriMorpho', null, [
        'disabled' => $hasBioMol && $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $options['action_type'] == Action::create(),
        ],
      ])
      ->add('typeIndividuVocFk', SpecimenVocType::class);

    if ($options['action_type'] != Action::create()) {
      $builder
        ->add('numIndBiomol', null, [
          'disabled' => $hasBioMol && $this->canEditAdminOnly($options),
        ])
        ->add('codeIndBiomol', null, [
          'disabled' => $hasBioMol && $this->canEditAdminOnly($options),
        ]);
    }

    $builder
      ->add('commentaireInd')
      ->add('especeIdentifiees', CollectionType::class, array(
        'entry_type' => EspeceIdentifieeEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array(
          'label' => false,
          'refTaxonLabel' => $options['refTaxonLabel'],
        ),
      ))
      ->addEventSubscriber($this->addUserDate);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Individu',
      'refTaxonLabel' => 'taxname',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'bbees_e3sbundle_individu';
  }
}
