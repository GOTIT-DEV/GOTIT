<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\Enums\Action;
use App\Form\Type\BaseVocType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\SearchableSelectType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChromatogramType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $pcr = $builder->getData()->getPcrFk();

    $builder
      ->add('pcrFk', SearchableSelectType::class, [
        'class' => 'App:Pcr',
        'choice_label' => 'codePcr',
        'placeholder' => $this->translator->trans("Pcr typeahead placeholder"),
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $pcr != null,
        ],
      ])

      ->add('code', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $options['action_type'] == Action::create(),
        ],
      ])
      ->add('yasNumber', TextType::class, array(
        'required' => true,
        'disabled' => $this->canEditAdminOnly($options),
      ))
      ->add('primerVocFk', BaseVocType::class, array(
        'voc_parent' => 'primerChromato',
        'placeholder' => 'Choose a primer',
        'disabled' => $this->canEditAdminOnly($options),
      ))
      ->add('qualityVocFk', BaseVocType::class, array(
        'voc_parent' => 'qualiteChromato',
        'placeholder' => 'Choose a quality',
      ))
      ->add('institutionFk', EntityType::class, array(
        'class' => 'App:Institution',
        'query_builder' => function (EntityRepository $er) {
          return $er->createQueryBuilder('institution')
            ->orderBy('institution.nomEtablissement', 'ASC');
        },
        'placeholder' => 'Choose a society',
        'choice_label' => 'nom_etablissement',
        'multiple' => false,
        'expanded' => false,
      ))
      ->add('comment');

  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Chromatogram',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'chromatogram';
  }
}
