<?php

namespace App\Form;

use App\Form\ActionFormType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('name')
      ->add('fullName', null, [
        'required' => false,
      ])
      ->add('alias', null, [
        'required' => false,
      ])
      ->add('institution', EntityType::class, [
        'class' => 'App:Institution',
        'placeholder' => 'Choose a Institution',
        'choice_label' => 'name',
        'query_builder' => function (EntityRepository $er) {
          return $er->createQueryBuilder('institution')
            ->orderBy('institution.name');
        },
        'multiple' => false,
        'expanded' => false,
        'required' => false,
      ])
      ->add('comment');

    $this->upperCaseFields($builder, [
      'name', 'fullName', 'alias',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Person',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'person';
  }
}
