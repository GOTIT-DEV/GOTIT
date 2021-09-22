<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\EmbedTypes\PersonEmbedType;
use App\Form\EmbedTypes\ProgramEmbedType;
use App\Form\Enums\Action;
use App\Form\Type\BaseVocType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\DynamicCollectionType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\SearchableSelectType;
use App\Form\Type\TaxnameType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SamplingType extends ActionFormType {

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $sampling = $builder->getData();
    $builder
      ->add('site', SearchableSelectType::class, [
        'class' => 'App:Site',
        'choice_label' => 'code',
        'placeholder' =>
        $this->translator->trans("Site typeahead placeholder"),
        'attr' => [
          "maxlength" => "255",
          'readonly' => ($options['action_type'] == Action::create() &&
            $sampling->getSite()),
        ],
      ])
      ->add('code', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'class' => 'sampling-code',
          'readonly' => $options['action_type'] == Action::create(),
        ],
      ])
      ->add('datePrecision', DatePrecisionType::class, [
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('date', DateFormattedType::class, [
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('methods', DynamicCollectionType::class, [
        'entry_type' => BaseVocType::class,
        'entry_options' => [
          'voc_parent' => 'method',
          'placeholder' => 'Choose a Sampling method',
        ],
      ])
      ->add('fixatives', DynamicCollectionType::class, [
        'entry_type' => BaseVocType::class,
        'entry_options' => [
          'voc_parent' => 'fixateur',
          'placeholder' => 'Choose a Fixateur',
        ],
      ])
      ->add('fundings', DynamicCollectionType::class, [
        'entry_type' => ProgramEmbedType::class,
        'attr' => [
          "data-allow-new" => true,
          "data-modal-controller" =>
          'App\\Controller\\Core\\ProgramController::newmodalAction',
        ],
      ])
      ->add('participants', DynamicCollectionType::class, [
        'entry_type' => PersonEmbedType::class,
        'attr' => [
          "data-allow-new" => true,
          "data-modal-controller" =>
          'App\\Controller\\Core\\PersonController::newmodalAction',
        ],
      ])
      ->add('targetTaxons', DynamicCollectionType::class, [
        'entry_type' => TaxnameType::class,
      ])
      ->add('durationMn', IntegerType::class, [
        'attr' => ["min" => "0"],
        'required' => false,
      ])
      ->add('temperatureC')
      ->add('conductanceMicroSieCm', IntegerType::class, [
        'attr' => ["min" => "0"],
        'required' => false,
      ])
      ->add('status', ChoiceType::class, [
        'choices' => ['YES' => 1, 'NO' => 0],
        'required' => true,
        'choice_translation_domain' => true,
        'multiple' => false,
        'expanded' => true,
        'label_attr' => ['class' => 'radio-inline'],
      ])
      ->add('comment')
      ->add('donation', BaseVocType::class, [
        'voc_parent' => 'leg',
        'sort_by_id' => true,
        'expanded' => true,
        'label_attr' => ['class' => 'radio-inline'],
      ]);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults([
      'data_class' => 'App\Entity\Sampling',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'sampling';
  }
}
