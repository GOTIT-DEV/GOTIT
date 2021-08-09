<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\Enums\Action;
use App\Form\Type\CountryVocType;
use App\Form\Type\EntityCodeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MunicipalityType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder
      ->add('code', EntityCodeType::class, [
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $options['action_type'] == Action::create(),
        ],
      ])
      ->add('name')
      ->add('region')
      ->add('countryFk', CountryVocType::class);

    $uppercase_fields = ['code', 'name', 'region'];
    $this->upperCaseFields($builder, $uppercase_fields);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Municipality',
      'country_id' => null,
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'municipality';
  }
}
