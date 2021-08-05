<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\Type\EntityCodeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReferentielTaxonType extends ActionFormType {

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {

    $builder
      ->add('taxname')
      ->add('taxon_full_name')
      ->add('rank')
      ->add('subclass')
      ->add('ordre')
      ->add('family')
      ->add('genus')
      ->add('species')
      ->add('subspecies')
      ->add('codeTaxon', EntityCodeType::class)
      ->add('clade')
      ->add('taxnameRef')
      ->add('validity', ChoiceType::class, array(
        'choices' => array('No' => 0, 'Yes' => 1),
        'required' => true,
        'multiple' => false,
        'expanded' => true,
        'label_attr' => array('class' => 'radio-inline'),
      ))
      ->add('commentaireRef');

    $uppercase_fields = [
      'taxname', 'rank', 'subclass', 'ordre', 'family',
      'genus', 'species', 'subspecies', 'clade', 'taxnameRef',
    ];
    $this->upperCaseFields($builder, $uppercase_fields);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\ReferentielTaxon',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'bbees_e3sbundle_referentieltaxon';
  }
}
