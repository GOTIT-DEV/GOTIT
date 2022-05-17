<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\EmbedTypes\EspeceIdentifieeEmbedType;
use App\Form\Enums\Action;
use App\Form\Type\BaseVocType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\SearchableSelectType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IndividuType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {

    $hasBioMol = (bool) $builder->getData()->getCodeIndBiomol();
    $bioMat = $builder->getData()->getLotMaterielFk();

    $builder
      ->add('lotMaterielFk', SearchableSelectType::class, [
        'class' => 'App:LotMateriel',
        'choice_label' => 'codeLotMateriel',
        'placeholder' => "Lotmateriel typeahead placeholder",
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
      ->add('typeIndividuVocFk', BaseVocType::class, [
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
      ->add('commentaireInd')
      ->add('especeIdentifiees', CollectionType::class, array(
        'entry_type' => EspeceIdentifieeEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array(
          'label' => false,
          'refTaxonLabel' => $options['refTaxonLabel'],
        ),
      ))
      ->addEventSubscriber($this->addUserDate);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Individu',
      'refTaxonLabel' => 'taxname',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix():string {
    return 'bbees_e3sbundle_individu';
  }
}
