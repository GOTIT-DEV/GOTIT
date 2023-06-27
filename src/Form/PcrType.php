<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\EmbedTypes\PcrEstRealiseParEmbedType;
use App\Form\Enums\Action;
use App\Form\Type\BaseVocType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\GeneType;
use App\Form\Type\SearchableSelectType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Adn;
use App\Controller\Core\PersonneController;

class PcrType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $adn = $builder->getData()->getAdnFk();
    $builder
      ->add('adnFk', SearchableSelectType::class, [
        'class' => Adn::class,
        'choice_label' => 'codeAdn',
        'placeholder' => $this->translator->trans("Adn typeahead placeholder"),
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $adn != null,
        ],
      ])
      ->add('codePcr', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $options['action_type'] == Action::create->value,
        ],
      ])
      ->add('numPcr', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('geneVocFk', GeneType::class)
      ->add('primerPcrStartVocFk', BaseVocType::class, array(
        'voc_parent' => 'primerPcrStart',
        'placeholder' => 'Choose a primer start',
        'disabled' => $this->canEditAdminOnly($options),
      ))
      ->add('primerPcrEndVocFk', BaseVocType::class, array(
        'voc_parent' => 'primerPcrEnd',
        'placeholder' => 'Choose a primer end',
        'disabled' => $this->canEditAdminOnly($options),
      ))
      ->add('datePrecisionVocFk', DatePrecisionType::class)
      ->add('datePcr', DateFormattedType::class)
      ->add('qualitePcrVocFk', BaseVocType::class, array(
        'voc_parent' => 'qualitePcr',
        'placeholder' => 'Choose a quality',
      ))
      ->add('specificiteVocFk', BaseVocType::class, array(
        'voc_parent' => 'specificite',
        'placeholder' => 'Choose a specificity',
      ))
      ->add('detailPcr')
      ->add('remarquePcr')
      ->add('pcrEstRealisePars', CollectionType::class, array(
        'entry_type' => PcrEstRealiseParEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array('label' => false),
        'attr' => [
          "data-allow-new" => true,
          "data-modal-controller" =>
          'App\\Controller\\Core\\PersonneController::newmodalAction',
        ],
      ))
      ->addEventSubscriber($this->addUserDate);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Pcr',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix(): string {
    return 'bbees_e3sbundle_pcr';
  }
}
