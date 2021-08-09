<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\EmbedTypes\TaxonIdentificationEmbedType;
use App\Form\Enums\Action;
use App\Form\Type\BaseVocType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\SearchableSelectType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpecimenType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {

    $hasBioMol = (bool) $builder->getData()->getCodeIndBiomol();
    $bioMat = $builder->getData()->getInternalLotFk();

    $builder
      ->add('internalLotFk', SearchableSelectType::class, [
        'class' => 'App:InternalLot',
        'choice_label' => 'codeLotMateriel',
        'placeholder' => "InternalLot typeahead placeholder",
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $bioMat != null,
        ],
      ])
      ->add('codeTube', EntityCodeType::class, [
        'disabled' => $hasBioMol && $this->canEditAdminOnly($options),
      ])
      ->add('codeIndTriMorpho', EntityCodeType::class, [
        'disabled' => $hasBioMol && $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $options['action_type'] == Action::create(),
        ],
      ])
      ->add('specimenTypeVocFk', BaseVocType::class, [
        'voc_parent' => 'typeIndividu',
        'placeholder' => 'Choose a Type',
      ]);

    if ($options['action_type'] != Action::create()) {
      $builder
        ->add('numIndBiomol', null, [
          'disabled' => $hasBioMol && $this->canEditAdminOnly($options),
        ])
        ->add('codeIndBiomol', EntityCodeType::class, [
          'disabled' => $hasBioMol && $this->canEditAdminOnly($options),
          'attr' => [
            'data-generate' => !$hasBioMol,
          ],
        ]);
    }

    $builder
      ->add('comment')
      ->add('taxonIdentifications', CollectionType::class, array(
        'entry_type' => TaxonIdentificationEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array(
          'label' => false,
          'refTaxonLabel' => $options['refTaxonLabel'],
        ),
      ));
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Specimen',
      'refTaxonLabel' => 'taxname',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'specimen';
  }
}
