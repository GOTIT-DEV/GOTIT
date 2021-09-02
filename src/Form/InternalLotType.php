<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\EmbedTypes\InternalLotContentEmbedType;
use App\Form\EmbedTypes\InternalLotProducerEmbedType;
use App\Form\EmbedTypes\InternalLotPublicationEmbedType;
use App\Form\EmbedTypes\TaxonIdentificationEmbedType;
use App\Form\Enums\Action;
use App\Form\Type\BaseVocType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\SearchableSelectType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InternalLotType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $sampling = $builder->getData()->getSamplingFk();

    $builder
      ->add('samplingFk', SearchableSelectType::class, [
        'class' => 'App:Sampling',
        'choice_label' => 'code',
        'placeholder' => $this->translator->trans("Sampling typeahead placeholder"),
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $sampling != null,
        ],
      ])
      ->add('code', EntityCodeType::class, [
        'attr' => [
          'readonly' => $options['action_type'] == Action::create(),
        ],
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('datePrecisionVocFk', DatePrecisionType::class)
      ->add('date', DateFormattedType::class)
      ->add('producers', CollectionType::class, array(
        'entry_type' => InternalLotProducerEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array('label' => false),
        'attr' => [
          "data-allow-new" => true,
          "data-modal-controller" => 'App\\Controller\\Core\\PersonController::newmodalAction',
        ],
      ))
      ->add('taxonIdentifications', CollectionType::class, array(
        'entry_type' => TaxonIdentificationEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array('label' => false),
      ))
      ->add('eyesVocFk', BaseVocType::class, array(
        'voc_parent' => 'yeux',
        'placeholder' => 'Choose a Eye',
      ))
      ->add('pigmentationVocFk', BaseVocType::class, array(
        'voc_parent' => 'pigmentation',
        'placeholder' => 'Choose a Pigmentation',
      ))
      ->add('status', ChoiceType::class, array(
        'choices' => array('NO' => 0, 'YES' => 1),
        'required' => true,
        'choice_translation_domain' => true,
        'multiple' => false,
        'expanded' => true,
        'label_attr' => array('class' => 'radio-inline'),
      ))
      ->add('sequencingAdvice')
      ->add('comment')
      ->add('storeFk', EntityType::class, array(
        'class' => 'App:Store',
        'query_builder' => function (EntityRepository $er) {
          return $er->createQueryBuilder('store')
            ->leftJoin('App:Voc', 'voc', 'WITH', 'store.storageTypeVocFk = voc.id')
            ->where('voc.code LIKE :codetype')
            ->setParameter('codetype', 'LOT')
            ->orderBy('LOWER(store.code)', 'ASC');
        },
        'placeholder' => 'Choose a Box',
        'choice_label' => 'code',
        'multiple' => false,
        'expanded' => false,
        'required' => false,
      ))
      ->add('contents', CollectionType::class, array(
        'entry_type' => InternalLotContentEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array('label' => false),
      ))
      ->add('publications', CollectionType::class, array(
        'entry_type' => InternalLotPublicationEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'required' => false,
        'entry_options' => array('label' => false),
      ));
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\InternalLot',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'internal_lot';
  }
}
