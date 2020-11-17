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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Form\Type\UppercaseType;
use App\Form\ActionFormType;

class ReferentielTaxonType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder
      ->add('taxname', UppercaseType::class)
      ->add('taxon_full_name')
      ->add('rank', UppercaseType::class)
      ->add('subclass', UppercaseType::class)
      ->add('ordre', UppercaseType::class)
      ->add('family', UppercaseType::class)
      ->add('genus', UppercaseType::class)
      ->add('species', UppercaseType::class)
      ->add('subspecies', UppercaseType::class)
      ->add('codeTaxon')
      ->add('clade', UppercaseType::class)
      ->add('taxnameRef', UppercaseType::class)
      ->add('validity', ChoiceType::class, array(
        'choices' => array('No' => 0, 'Yes' => 1),
        'required' => true,
        'multiple' => false,
        'expanded' => true,
        'label_attr' => array('class' => 'radio-inline'),
      ))
      ->add('commentaireRef')
      ->addEventSubscriber($this->addUserDate);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\ReferentielTaxon',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'bbees_e3sbundle_referentieltaxon';
  }
}
