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
use App\Form\Type\SequenceStatusType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\DateFormattedType;
use App\Form\Enums\Action;
use App\Form\EmbedTypes\SqcEstPublieDansEmbedType;
use App\Form\EmbedTypes\SequenceAssembleeEstRealiseParEmbedType;
use App\Form\EmbedTypes\EstAligneEtTraiteEmbedType;
use App\Form\EmbedTypes\EspeceIdentifieeEmbedType;

class SequenceAssembleeType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $sequence = $builder->getData();
    $gene     = $options['gene'] ?: $sequence->getGeneVocFk();
    $specimen = $options['specimen'] ?: $sequence->getIndividuFk();

    $builder
      ->add('codeSqcAss', null, [
        'attr' => ['readonly' => Action::create() == $options['action_type']],
      ])
      ->add('accessionNumber')
      ->add('codeSqcAlignement', null, [
        'attr' => ['readonly' => Action::create() == $options['action_type']],
        'data' => $options['action_type'] == Action::create()
        ? $this->translator->trans('Auto generated code')
        : $sequence->getCodeSqcAlignement(),
      ])
      ->add('commentaireSqcAss')
      ->add('datePrecisionVocFk', DatePrecisionType::class)
      ->add('dateCreationSqcAss', DateFormattedType::class)
      ->add('statutSqcAssVocFk', SequenceStatusType::class)
      ->add('estAligneEtTraites', CollectionType::class, array(
        'entry_type'     => EstAligneEtTraiteEmbedType::class,
        'allow_add'      => true,
        'allow_delete'   => true,
        'prototype'      => true,
        'prototype_name' => '__name__',
        'by_reference'   => false,
        'entry_options'  => array(
          'label'      => false,
          'geneVocFk'  => $gene,
          'individuFk' => $specimen,
        ),
      ))
      ->add('sequenceAssembleeEstRealisePars', CollectionType::class, array(
        'entry_type'     => SequenceAssembleeEstRealiseParEmbedType::class,
        'allow_add'      => true,
        'allow_delete'   => true,
        'prototype'      => true,
        'prototype_name' => '__name__',
        'by_reference'   => false,
        'entry_options'  => array('label' => false),
        'attr'           => [
          "data-allow-new"        => true,
          "data-modal-controller" => 'App\\Controller\\Core\\PersonneController::newmodalAction',
        ],
      ))
      ->add('especeIdentifiees', CollectionType::class, array(
        'entry_type'     => EspeceIdentifieeEmbedType::class,
        'allow_add'      => true,
        'allow_delete'   => true,
        'prototype'      => true,
        'prototype_name' => '__name__',
        'by_reference'   => false,
        'entry_options'  => array('label' => false),
      ))
      ->add('sqcEstPublieDanss', CollectionType::class, array(
        'entry_type'     => SqcEstPublieDansEmbedType::class,
        'allow_add'      => true,
        'allow_delete'   => true,
        'prototype'      => true,
        'prototype_name' => '__name__',
        'by_reference'   => false,
        'required'       => false,
        'entry_options'  => array('label' => false),
      ))
      ->addEventsubscriber($this->addUserDate);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults([
      'data_class' => 'App\Entity\SequenceAssemblee',
      'gene'       => null,
      'specimen'   => null,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'sequence_assemblee';
  }
}
