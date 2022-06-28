<?php

namespace App\Form\Type;

use App\Form\ActionFormType;
use App\Form\Enums\Action;
use App\Form\Type\GeneType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GeneSpecimenType extends ActionFormType {

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $form_data = $builder->getData();
    $gene = $form_data['geneVocFk'];
    $specimen = $form_data['individuFk'];

    $builder
      ->add('geneVocFk', GeneType::class, [
        'query_builder' =>
        function (EntityRepository $er) use ($gene) {
          $qb = $er->createQueryBuilder('voc')
            ->where("voc.parent = 'gene'")
            ->orderBy('voc.libelle', 'ASC');
          if ($gene) {
            $qb = $qb
              ->andWhere('voc.id = :geneVocFk')
              ->setParameter('geneVocFk', $gene->getId());
          }

          return $qb;
        },
      ])
      ->add('individuFk', SearchableSelectType::class, [
        'class' => 'App:Individu',
        'choice_label' => 'codeIndBiomol',
        'placeholder' => $this->translator->trans("Individu typeahead placeholder"),
        'attr' => [
          'readonly' => $this->canEditAdminOnly($options) || $specimen != null,
        ],
      ]);

    if ($options['action_type'] != Action::show->value) {
      $builder->add('button_valid', SubmitType::class, array(
        'label' => 'button.Valid',
        'attr' => ['class' => 'btn btn-round btn-success'],
      ));
    }

  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix():string {
    return 'gene_specimen_form';
  }
}
