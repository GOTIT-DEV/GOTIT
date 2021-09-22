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
    $gene = $form_data['gene'];
    $specimen = $form_data['specimen'];

    $builder
      ->add('gene', GeneType::class, [
        'query_builder' =>
        function (EntityRepository $er) use ($gene) {
          $qb = $er->createQueryBuilder('voc')
            ->where("voc.parent = 'gene'")
            ->orderBy('voc.label', 'ASC');
          if ($gene) {
            $qb = $qb
              ->andWhere('voc.id = :gene')
              ->setParameter('gene', $gene->getId());
          }

          return $qb;
        },
      ])
      ->add('specimen', SearchableSelectType::class, [
        'class' => 'App:Specimen',
        'choice_label' => 'molecularCode',
        'placeholder' => $this->translator->trans("Specimen typeahead placeholder"),
        'attr' => [
          'readonly' => $this->canEditAdminOnly($options) || $specimen != null,
        ],
      ]);

    if ($options['action_type'] != Action::show()) {
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
  public function getBlockPrefix() {
    return 'gene_specimen_form';
  }
}
