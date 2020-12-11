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

use App\Form\ActionFormType;
use App\Form\Type\BaseVocType;
use App\Form\Type\CountryVocType;
use App\Form\Type\ModalButtonType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StationType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $station = $builder->getData();
    $builder
      ->add('codeStation', null, [
        "disabled" => $this->canEditAdminOnly($options),
      ])
      ->add('nomStation')
      ->add('infoDescription')
      ->add('paysFk', CountryVocType::class)
      ->add('communeFk', EntityType::class, array(
        'class' => 'App:Commune',
        'query_builder' => function (EntityRepository $er) use ($station) {
          $query = $er->createQueryBuilder('commune')
            ->orderBy('commune.codeCommune', 'ASC');
          if ($station->getPaysFk()) {
            $query = $query->where('commune.paysFk = :country')
              ->setParameter('country', $station->getPaysFk()->getId());
          }
          return $query;
        },
        'choice_label' => 'codeCommune',
        'multiple' => false,
        'expanded' => false,
        'placeholder' => 'Choose a Commune',
      ))
      ->add('newMunicipality', ModalButtonType::class, [
        'attr' => [
          'class' => "btn-info btn-sm",
          "data-modal-controller" => 'App\\Controller\\Core\\CommuneController::newmodalAction',
        ],
      ])
      ->add('habitatTypeVocFk', BaseVocType::class, array(
        'voc_parent' => 'habitatType',
        'placeholder' => 'Choose an Habitat Type',
      ))
      ->add('pointAccesVocFk', BaseVocType::class, array(
        'voc_parent' => 'pointAcces',
        'placeholder' => 'Choose an Access Point',
      ))
      ->add('latDegDec', NumberType::class, array(
        'required' => true,
        'scale' => 6,
      ))
      ->add('longDegDec', NumberType::class, array(
        'required' => true,
        'scale' => 6,
      ))
      ->add('showProximalSites', ModalButtonType::class, [
        'attr' => [
          'class' => "btn-info btn-sm",
          'data-target' => "#map-modal",
        ],
        'icon_class' => 'fa-crosshairs',
      ])
      ->add('precisionLatLongVocFk', BaseVocType::class, array(
        'voc_parent' => 'precisionLatLong',
        'placeholder' => 'Choose a GPS Distance Quality',
        "sort_by_id" => true,
      ))
      ->add('altitudeM')
      ->add('commentaireStation')
      ->addEventSubscriber($this->addUserDate);

    $this->upperCaseFields($builder, [
      'codeStation', 'nomStation', 'nomPersonneRef',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Station',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'station';
  }
}
