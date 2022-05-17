<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\EmbedTypes\AdnEstRealiseParEmbedType;
use App\Form\Type\BaseVocType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\SearchableSelectType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdnType extends ActionFormType {

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $specimen = $builder->getData()->getIndividuFk();
    $builder
      ->add('individuFk', SearchableSelectType::class, [
        'class' => 'App:Individu',
        'choice_label' => 'codeIndBioMol',
        'placeholder' => $this->translator->trans("Individu typeahead placeholder"),
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $specimen != null,
        ],
      ])
      # Is not auto-generated : editable in create mode
      ->add('codeAdn', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('datePrecisionVocFk', DatePrecisionType::class)
      ->add('dateAdn', DateFormattedType::class)
      ->add('methodeExtractionAdnVocFk', BaseVocType::class, [
        'voc_parent' => "methodeExtractionAdn",
        'placeholder' => 'Choose a method',
      ])
      ->add('concentrationNgMicrolitre', NumberType::class, array(
        'scale' => 4,
        'required' => false,
      ))
      ->add('commentaireAdn')
      ->add('qualiteAdnVocFk', BaseVocType::class, [
        'voc_parent' => 'qualiteAdn',
        'placeholder' => 'Choose a quality',
      ])
      ->add('boiteFk', EntityType::class, array(
        'class' => 'App:Boite',
        'query_builder' => function (EntityRepository $er) {
          return $er->createQueryBuilder('boite')
            ->leftJoin('App:Voc', 'voc', 'WITH', 'boite.typeBoiteVocFk = voc.id')
            ->where('voc.code LIKE :codetype')
            ->setParameter('codetype', 'ADN')
            ->orderBy('LOWER(boite.codeBoite)', 'ASC');
        },
        'placeholder' => 'Choose a Box',
        'choice_label' => 'codeBoite',
        'multiple' => false,
        'expanded' => false,
        'required' => false,
      ))
      ->add('adnEstRealisePars', CollectionType::class, [
        'entry_type' => AdnEstRealiseParEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array('label' => false),
        'attr' => [
          "data-allow-new" => true,
          "data-modal-controller" => 'App\\Controller\\Core\\PersonneController::newmodalAction',
        ],
      ])
      ->addEventSubscriber($this->addUserDate);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Adn',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix():string {
    return 'bbees_e3sbundle_adn';
  }
}
