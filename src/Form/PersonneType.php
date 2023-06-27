<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ActionFormType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Etablissement;

class PersonneType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $user = $this->security->getUser();
    $builder->add('nomPersonne')
      ->add('nomComplet', null, [
        'required' => false,
      ])
      ->add('nomPersonneRef', null, [
        'required' => false,
      ])
      ->add('etablissementFk', EntityType::class, [
        'class' => Etablissement::class,
        'placeholder' => 'Choose a Etablissement',
        'choice_label' => 'nom_etablissement',
        'query_builder' => function (EntityRepository $er) {
          return $er->createQueryBuilder('institution')
            ->orderBy('institution.nomEtablissement');
        },
        'multiple' => false,
        'expanded' => false,
        'required' => false,
      ]);
    if ($user instanceof User) {
      $builder->add('commentairePersonne');
    }

    $builder->addEventSubscriber($this->addUserDate);

    $this->upperCaseFields($builder, [
      'nomPersonne', 'nomComplet', 'nomPersonneRef',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Personne',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix(): string {
    return 'bbees_e3sbundle_personne';
  }
}
