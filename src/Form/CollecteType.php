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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

use App\Form\Type\UppercaseType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\DateFormattedType;

use App\Form\Enums\Action;
use App\Form\EmbedTypes\EstFinanceParEmbedType;
use App\Form\EmbedTypes\EstEffectueParEmbedType;
use App\Form\EmbedTypes\APourSamplingMethodEmbedType;
use App\Form\EmbedTypes\APourFixateurEmbedType;
use App\Form\EmbedTypes\ACiblerEmbedType;
use App\Form\ActionFormType;

class CollecteType extends ActionFormType
{

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $station = $builder->getData()->getStationFk();
    $codeStation = $station ? $station->getCodeStation() : null;
    $idStation = $station ? $station->getId() : null;

    $builder
      ->add('stationTypeahead', UppercaseType::class, [
        'mapped' => false,
        'attr' => [
          'class' => 'typeahead typeahead-station',
          'data-target_id' => $this->getBlockPrefix() . "_stationId",
          'name' => "where",
          'placeholder' => "Station typeahead placeholder",
          "maxlength" => "255",
          'data-initial' => $codeStation
        ],
        'disabled' => $this->canEditAdminOnly($options),
        'required' => true,
        'data' => $codeStation,
      ])
      ->add('stationId', HiddenType::class, [
        'mapped' => false,
        'required' => true,
        'attr' => [
          'class' => 'station-id',
          'data-initial' => $idStation
        ],
        'data' => $idStation,
      ])
      ->add('codeCollecte', null, [
        'attr' => [
          'class' => 'sampling-code',
          // Using readonly instead of disabled so that generated code can be stored in DB
          'readonly' => ($this->canEditAdminOnly($options) ||
            $options['action_type'] == Action::create())
        ],
      ])
      ->add('datePrecisionVocFk', DatePrecisionType::class, [
        'disabled' => $this->canEditAdminOnly($options)
      ])
      ->add('dateCollecte', DateFormattedType::class, [
        'disabled' => $this->canEditAdminOnly($options)
      ])
      ->add('aPourSamplingMethods', CollectionType::class, [
        'entry_type' => APourSamplingMethodEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => [
          "label" => false,
        ]
      ])
      ->add('aPourFixateurs', CollectionType::class, [
        'entry_type' => APourFixateurEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => ['label' => false]
      ])
      ->add('estFinancePars', CollectionType::class, [
        'entry_type' => EstFinanceParEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'attr' => [
          "data-allow-new" => true,
          "render_controller" => 'App\\Controller\\Core\\ProgrammeController::newmodalAction'
        ],
        'entry_options' => array('label' => false)
      ])
      ->add('estEffectuePars', CollectionType::class, [
        'entry_type' => EstEffectueParEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'attr' => [
          "data-allow-new" => true,
          "data-modal-controller" => 'App\\Controller\\Core\\PersonneController::newmodalAction'
        ],
        'entry_options' => array('label' => false)
      ])
      ->add('aCiblers', CollectionType::class, [
        'entry_type' => ACiblerEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array('label' => false)
      ])
      ->add('dureeEchantillonnageMn', IntegerType::class, [
        'attr' => ["min" => "0"],
        'required' => false
      ])
      ->add('temperatureC')
      ->add('conductiviteMicroSieCm', IntegerType::class, [
        'attr' => ["min" => "0"],
        'required' => false
      ])
      ->add('aFaire', ChoiceType::class, [
        'choices'  => ['YES' => 1, 'NO' => 0,],
        'required' => true,
        'choice_translation_domain' => true,
        'multiple' => false,
        'expanded' => true,
        'label_attr' => ['class' => 'radio-inline'],
      ])
      ->add('commentaireCollecte')
      ->add('legVocFk', EntityType::class, [
        'class' => 'App:Voc',
        'query_builder' => function (EntityRepository $er) {
          return $er->createQueryBuilder('voc')
            ->where('voc.parent LIKE :parent')
            ->setParameter('parent', 'leg')
            ->orderBy('voc.libelle', 'DESC');
        },
        'choice_translation_domain' => true,
        'choice_label' => 'libelle',
        'multiple' => false,
        'expanded' => true,
        'label_attr' => ['class' => 'radio-inline']
      ])
      ->addEventSubscriber($this->addUserDate);
  }


  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    parent::configureOptions($resolver);
    $resolver->setDefaults([
      'data_class' => 'App\Entity\Collecte',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix()
  {
    return 'bbees_e3sbundle_collecte';
  }
}
