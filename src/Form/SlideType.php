<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\EmbedTypes\PersonEmbedType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\DynamicCollectionType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\SearchableSelectType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SlideType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $specimen = $builder->getData()->getSpecimen();

    $builder
      ->add('specimen', SearchableSelectType::class, [
        'class' => 'App:Specimen',
        'choice_label' => 'morphologicalCode',
        'placeholder' => $this->translator
          ->trans("Specimen morphologicalCode typeahead placeholder"),
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $specimen != null,
        ],
      ])
      ->add('code', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('label')
      ->add('datePrecision', DatePrecisionType::class)
      ->add('date', DateFormattedType::class)
      ->add('pictureFolder')
      ->add('comment')
      ->add('store', EntityType::class, array(
        'class' => 'App:Store',
        'query_builder' => function (EntityRepository $er) {
          return $er->createQueryBuilder('store')
            ->leftJoin('App:Voc', 'voc', 'WITH', 'store.storageType = voc.id')
            ->where('voc.code LIKE :codetype')
            ->setParameter('codetype', 'LAME')
            ->orderBy('LOWER(store.code)', 'ASC');
        },
        'placeholder' => 'Choose a Box',
        'choice_label' => 'code',
        'multiple' => false,
        'expanded' => false,
        'required' => false,
      ))
      ->add('producers', DynamicCollectionType::class, array(
        'label' => "Slide producers",
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
      'data_class' => 'App\Entity\Slide',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'slide';
  }
}
