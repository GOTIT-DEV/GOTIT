<?php

namespace App\Form\EmbedTypes;

use App\Form\Type\BaseVocType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\TaxnameType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EspeceIdentifieeEmbedType extends AbstractType {

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {

    $builder
      ->add('referentielTaxonFk', TaxnameType::class, [
        'choice_label' => $options['refTaxonLabel'],
      ])
      ->add('critereIdentificationVocFk', BaseVocType::class, array(
        'voc_parent' => 'critereIdentification',
        'expanded' => true,
        'attr' => ["class" => "stacked"],
        'label_attr' => array('class' => 'radio-inline'),
        'required' => true,
      ))
      ->add('dateIdentification', DateFormattedType::class)
      ->add('datePrecisionVocFk', DatePrecisionType::class)
      ->add('typeMaterielVocFk', EntityType::class, array(
        'class' => 'App:Voc',
        'query_builder' => function (EntityRepository $er) {
          return $er->createQueryBuilder('voc')
            ->where('voc.parent LIKE :parent')
            ->setParameter('parent', 'typeMateriel')
            ->orderBy('voc.id', 'ASC');
        },
        'choice_translation_domain' => true,
        'choice_label' => 'libelle',
        'multiple' => false,
        'expanded' => true,
        'label_attr' => array('class' => 'radio-inline'),
        'required' => true,
      ))
      ->add('commentaireEspId')
      ->add('personSpeciesIds', CollectionType::class, array(
        'entry_type' => PersonSpeciesIdEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name_inner__',
        'by_reference' => false,
        'entry_options' => array('label' => false),
      ));
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\EspeceIdentifiee',
      'refTaxonLabel' => 'taxname',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'bbees_e3sbundle_especeidentifiee';
  }
}
