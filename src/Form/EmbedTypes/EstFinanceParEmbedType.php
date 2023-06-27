<?php

namespace App\Form\EmbedTypes;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EstFinanceParEmbedType extends AbstractType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('programmeFk', EntityType::class, array(
      'class' => Programme::class,
      'query_builder' => function (EntityRepository $er) {
        return $er->createQueryBuilder('programme')
          ->orderBy('programme.codeProgramme', 'ASC');
      },
      'choice_label' => 'code_programme',
      'multiple' => false,
      'expanded' => false,
      'label' => false,
      'placeholder' => 'Choose a Program',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\EstFinancePar',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix(): string {
    return 'bbees_e3sbundle_estfinancepar';
  }
}
