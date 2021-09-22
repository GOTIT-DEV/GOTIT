<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\EmbedTypes\PersonEmbedType;
use App\Form\Enums\Action;
use App\Form\Type\BaseVocType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\DynamicCollectionType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\GeneType;
use App\Form\Type\SearchableSelectType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PcrType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $dna = $builder->getData()->getDna();
    $builder
      ->add('dna', SearchableSelectType::class, [
        'class' => 'App:Dna',
        'choice_label' => 'code',
        'placeholder' => $this->translator->trans("Dna typeahead placeholder"),
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $dna != null,
        ],
      ])
      ->add('code', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $options['action_type'] == Action::create(),
        ],
      ])
      ->add('number', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('gene', GeneType::class)
      ->add('primerStart', BaseVocType::class, array(
        'voc_parent' => 'primerPcrStart',
        'placeholder' => 'Choose a primer start',
        'disabled' => $this->canEditAdminOnly($options),
      ))
      ->add('primerEnd', BaseVocType::class, array(
        'voc_parent' => 'primerPcrEnd',
        'placeholder' => 'Choose a primer end',
        'disabled' => $this->canEditAdminOnly($options),
      ))
      ->add('datePrecision', DatePrecisionType::class)
      ->add('date', DateFormattedType::class)
      ->add('quality', BaseVocType::class, array(
        'voc_parent' => 'qualitePcr',
        'placeholder' => 'Choose a quality',
      ))
      ->add('specificity', BaseVocType::class, array(
        'voc_parent' => 'specificite',
        'placeholder' => 'Choose a specificity',
      ))
      ->add('details')
      ->add('comment')
      ->add('producers', DynamicCollectionType::class, array(
        'entry_type' => PersonEmbedType::class,
        'attr' => [
          "data-allow-new" => true,
          "data-modal-controller" =>
          'App\\Controller\\Core\\PersonController::newmodalAction',
        ],
      ));
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
  public function getBlockPrefix() {
    return 'pcr';
  }
}
