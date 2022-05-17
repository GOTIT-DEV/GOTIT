<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\EmbedTypes\EspeceIdentifieeEmbedType;
use App\Form\EmbedTypes\LotMaterielExtEstRealiseParEmbedType;
use App\Form\EmbedTypes\LotMaterielExtEstReferenceDansEmbedType;
use App\Form\Enums\Action;
use App\Form\Type\BaseVocType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\SearchableSelectType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LotMaterielExtType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $sampling = $builder->getData()->getCollecteFk();

    $builder
      ->add('collecteFk', SearchableSelectType::class, [
        'class' => 'App:Collecte',
        'choice_label' => 'codeCollecte',
        'placeholder' => $this->translator->trans("Collecte typeahead placeholder"),
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $sampling != null,
        ],
      ])
      ->add('codeLotMaterielExt', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
        "attr" => [
          'readonly' => ($options['action_type'] == Action::create()),
        ],
      ])
      ->add('pigmentationVocFk', BaseVocType::class, [
        'voc_parent' => 'pigmentation',
        'placeholder' => 'Choose a Pigmentation',
      ])
      ->add('yeuxVocFk', BaseVocType::class, [
        'voc_parent' => 'yeux',
        'placeholder' => 'Choose a Eye',
      ])
      ->add('commentaireLotMaterielExt')
      ->add('nbIndividusVocFk', BaseVocType::class, [
        'voc_parent' => 'nbIndividus',
        'placeholder' => 'Choose an option',
      ])
      ->add('commentaireNbIndividus')
      ->add('datePrecisionVocFk', DatePrecisionType::class)
      ->add('dateCreationLotMaterielExt', DateFormattedType::class)
      ->add('lotMaterielExtEstRealisePars', CollectionType::class, [
        'entry_type' => LotMaterielExtEstRealiseParEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => ['label' => false],
        'attr' => [
          "data-allow-new" => true,
          "data-modal-controller" => 'App\\Controller\\Core\\PersonneController::newmodalAction',
        ],
      ])
      ->add('especeIdentifiees', CollectionType::class, [
        'entry_type' => EspeceIdentifieeEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => ['label' => false],
      ])
      ->add('lotMaterielExtEstReferenceDanss', CollectionType::class, [
        'entry_type' => LotMaterielExtEstReferenceDansEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => ['label' => false],
      ])
      ->addEventSubscriber($this->addUserDate);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(['data_class' => 'App\Entity\LotMaterielExt']);
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix():string {
    return 'bbees_e3sbundle_lotmaterielext';
  }
}
