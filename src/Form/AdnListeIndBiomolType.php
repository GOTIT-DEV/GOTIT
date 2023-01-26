<?php

namespace App\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdnListeIndBiomolType extends AbstractType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('individuFk', EntityType::class, array(
      'class' => 'App:Individu',
      'query_builder' => function (EntityRepository $er) {
        return $er->createQueryBuilder('ind')
          ->where('ind.codeIndBiomol IS NOT NULL')
          ->orderBy('ind.codeIndBiomol', 'ASC');
      },
      'placeholder' => 'Choose an individu',
      'choice_label' => 'code_ind_biomol',
      'multiple' => false,
      'expanded' => false,
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
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
