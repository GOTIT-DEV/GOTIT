<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\Type\EntityCodeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgrammeType extends ActionFormType {

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('codeProgramme', EntityCodeType::class)
      ->add('nomProgramme')
      ->add('nomsResponsables')
      ->add('typeFinanceur')
      ->add('anneeDebut', null, [
        'attr' => [
          'min' => 1900,
          'max' => 3000,
        ],
      ])
      ->add('anneeFin', null, [
        'attr' => [
          'min' => 1900,
          'max' => 3000,
        ],
      ])
      ->add('commentaireProgramme')
      ->addEventSubscriber($this->addUserDate);

    $this->upperCaseFields($builder, ['codeProgramme']);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Programme',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'bbees_e3sbundle_programme';
  }
}
