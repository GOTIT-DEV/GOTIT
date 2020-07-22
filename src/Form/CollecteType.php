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

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use App\Form\APourSamplingMethodEmbedType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\EventListener\AddUserDateFields;

use App\Form\ActionFormType;
use Symfony\Component\Security\Core\Security;

class CollecteType extends ActionFormType
{

  private $addUserDate;

  /**
   * {@inheritdoc}
   */
  public function __construct(TokenStorageInterface $tokenStorage, Security $security)
  {
    $this->addUserDate = new AddUserDateFields($tokenStorage);
    $this->security = $security;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $action_type = $options['action_type'];
    $editAdminOnly = ($action_type == "edit" && !$this->security->isGranted('ROLE_ADMIN'));

    $station = $builder->getData()->getStationFk();
    $codeStation = $station ? $station->getCodeStation() : null;
    $idStation = $station ? $station->getId():null;

    $builder
      ->add('stationTypeahead', null, [
        'mapped' => false,
        'attr' => [
          'class' => 'typeahead typeahead-station text-uppercase',
          'data-target_id' => "bbees_e3sbundle_collecte_stationId",
          'name' => "where",
          'placeholder' => "Station typeahead placeholder",
          "maxlength" => "255",
          'readonly' => $editAdminOnly,
          'data-initial' => $codeStation
        ],
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
          'readonly' => $editAdminOnly || $action_type == Action::create()
        ],
      ])
      ->add('datePrecisionVocFk', DatePrecisionType::class, [
        /* Using mock readonly option to avoid overriding attr option
          See DatePrecisionType definition */
        'readonly' => $editAdminOnly
      ])
      ->add('dateCollecte', DateFormattedType::class, [
        'attr' => ['readonly' => $editAdminOnly]
      ])
      ->add('aPourSamplingMethods', CollectionType::class, [
        'entry_type' => APourSamplingMethodEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'attr' => [
          "data-allow-new" => false
        ],
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
          "data-modal-controller" => 'App\\Controller\\Core\\ProgrammeController::newmodalAction'
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
