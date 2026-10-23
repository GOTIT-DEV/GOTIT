<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\Type\EntityCodeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VocType extends ActionFormType {

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options): void {

    $qb = $this->er->createQueryBuilder();
    $query = $qb->select('voc.parent')
      ->from(\App\Entity\Voc::class, 'voc')
      ->groupBy('voc.parent')
      ->orderBy('voc.parent')
      ->getQuery();

    $choices = array_column($query->getScalarResult(), "parent");
    usort(
      $choices,
      function ($a, $b) {
        return strcmp(
          $this->translator->trans(sprintf('vocParent.%s', $a)),
          $this->translator->trans(sprintf('vocParent.%s', $b))
        );
      }
    );

    $builder
      ->add('code', EntityCodeType::class)
      ->add('libelle')
      ->add('parent', ChoiceType::class, [
        'choices' => $choices,
        "choice_label" => function ($choice, $key, $value) {
          return sprintf("vocParent.%s", $value);
        },
        'placeholder' => 'Choose a Parent',
        'required' => true,
        'choice_translation_domain' => true,
        'multiple' => false,
        'expanded' => false,
      ])
      ->add('commentaire')
      ->addEventSubscriber($this->addUserDate);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver): void {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Voc',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix(): string {
    return 'bbees_e3sbundle_voc';
  }
}
