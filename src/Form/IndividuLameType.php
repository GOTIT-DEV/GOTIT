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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use App\Form\Type\SearchableSelectType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\DateFormattedType;
use App\Form\EmbedTypes\IndividuLameEstRealiseParEmbedType;
use App\Form\ActionFormType;

class IndividuLameType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $specimen = $builder->getData()->getIndividuFk();

    $builder
      ->add('individuFk', SearchableSelectType::class, [
        'class' => 'App:Individu',
        'choice_label' => 'codeIndTriMorpho',
        'placeholder' => $this->translator
          ->trans("Individu codeIndTriMorpho typeahead placeholder"),
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $specimen != null,
        ],
      ])
      ->add('codeLameColl', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('libelleLame')
      ->add('datePrecisionVocFk', DatePrecisionType::class)
      ->add('dateLame', DateFormattedType::class)
      ->add('nomDossierPhotos')
      ->add('commentaireLame')
      ->add('boiteFk', EntityType::class, array(
        'class' => 'App:Boite',
        'query_builder' => function (EntityRepository $er) {
          return $er->createQueryBuilder('boite')
            ->leftJoin('App:Voc', 'voc', 'WITH', 'boite.typeBoiteVocFk = voc.id')
            ->where('voc.code LIKE :codetype')
            ->setParameter('codetype', 'LAME')
            ->orderBy('LOWER(boite.codeBoite)', 'ASC');
        },
        'placeholder' => 'Choose a Box',
        'choice_label' => 'codeBoite',
        'multiple' => false,
        'expanded' => false,
        'required' => false,
      ))
      ->add('individuLameEstRealisePars', CollectionType::class, array(
        'entry_type' => IndividuLameEstRealiseParEmbedType::class,
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
      'data_class' => 'App\Entity\IndividuLame',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'bbees_e3sbundle_individulame';
  }
}
