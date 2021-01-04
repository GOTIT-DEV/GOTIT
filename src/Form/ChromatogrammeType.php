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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use App\Form\Type\SearchableSelectType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\BaseVocType;
use App\Form\Enums\Action;
use App\Form\ActionFormType;

class ChromatogrammeType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $pcr = $builder->getData()->getPcrFk();

    $builder
      ->add('pcrFk', SearchableSelectType::class, [
        'class' => 'App:Pcr',
        'choice_label' => 'codePcr',
        'placeholder' => $this->translator->trans("Pcr typeahead placeholder"),
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $pcr != null,
        ],
      ])

      ->add('codeChromato', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $options['action_type'] == Action::create(),
        ],
      ])
      ->add('numYas', TextType::class, array(
        'required' => true,
        'disabled' => $this->canEditAdminOnly($options),
      ))
      ->add('primerChromatoVocFk', BaseVocType::class, array(
        'voc_parent' => 'primerChromato',
        'placeholder' => 'Choose a primer',
        'disabled' => $this->canEditAdminOnly($options),
      ))
      ->add('qualiteChromatoVocFk', BaseVocType::class, array(
        'voc_parent' => 'qualiteChromato',
        'placeholder' => 'Choose a quality',
      ))
      ->add('etablissementFk', EntityType::class, array(
        'class' => 'App:Etablissement',
        'query_builder' => function (EntityRepository $er) {
          return $er->createQueryBuilder('etablissement')
            ->orderBy('etablissement.nomEtablissement', 'ASC');
        },
        'placeholder' => 'Choose a society',
        'choice_label' => 'nom_etablissement',
        'multiple' => false,
        'expanded' => false,
      ))
      ->add('commentaireChromato')
      ->addEventsubscriber($this->addUserDate);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Chromatogramme',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'bbees_e3sbundle_chromatogramme';
  }
}
