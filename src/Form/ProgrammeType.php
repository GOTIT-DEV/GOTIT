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
use App\Form\Type\EntityCodeType;
use App\Form\ActionFormType;

class ProgrammeType extends ActionFormType {

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('codeProgramme', EntityCodeType::class)
      ->add('nomProgramme')
      ->add('nomsResponsables')
      ->add('typeFinanceur')
      ->add('anneeDebut', null, [
        'attr' => [
          'min' => 1900,
          'max' => 3000,
        ],
      ])
      ->add('anneeFin', null, [
        'attr' => [
          'min' => 1900,
          'max' => 3000,
        ],
      ])
      ->add('commentaireProgramme')
      ->addEventSubscriber($this->addUserDate);

    $this->upperCaseFields($builder, ['codeProgramme']);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Programme',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'bbees_e3sbundle_programme';
  }
}
