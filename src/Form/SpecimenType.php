<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\EmbedTypes\TaxonIdentificationEmbedType;
use App\Form\Enums\Action;
use App\Form\Type\BaseVocType;
use App\Form\Type\DynamicCollectionType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\SearchableSelectType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpecimenType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {

    $hasBioMol = (bool) $builder->getData()->getMolecularCode();
    $bioMat = $builder->getData()->getInternalLot();

    $builder
      ->add('internalLot', SearchableSelectType::class, [
        'class' => 'App:InternalLot',
        'choice_label' => 'code',
        'placeholder' => "InternalLot typeahead placeholder",
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $bioMat != null,
        ],
      ])
      ->add('tubeCode', EntityCodeType::class, [
        'disabled' => $hasBioMol && $this->canEditAdminOnly($options),
      ])
      ->add('morphologicalCode', EntityCodeType::class, [
        'disabled' => $hasBioMol && $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $options['action_type'] == Action::create(),
        ],
      ])
      ->add('specimenType', BaseVocType::class, [
        'voc_parent' => 'typeIndividu',
        'placeholder' => 'Choose a Type',
      ]);

    if ($options['action_type'] != Action::create()) {
      $builder
        ->add('molecularNumber', null, [
          'disabled' => $hasBioMol && $this->canEditAdminOnly($options),
        ])
        ->add('molecularCode', EntityCodeType::class, [
          'disabled' => $hasBioMol && $this->canEditAdminOnly($options),
          'attr' => [
            'data-generate' => !$hasBioMol,
          ],
        ]);
    }

    $builder
      ->add('comment')
      ->add('taxonIdentifications', DynamicCollectionType::class, array(
        'entry_type' => TaxonIdentificationEmbedType::class,
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
