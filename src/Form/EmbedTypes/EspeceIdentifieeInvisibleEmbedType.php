<?php

namespace App\Form\EmbedTypes;

use App\Form\Type\BaseVocType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\TaxnameType;
use App\Form\UserDateTraceType;
use App\Form\ActionFormType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class EspeceIdentifieeInvisibleEmbedType extends ActionFormType {

  /**
   * {@inheritdoc}
   */   
    
  public function buildForm(FormBuilderInterface $builder, array $options) {
      
    $this->taxon_code_default = ( isset($this->config['internal_biological_material']['taxon_code_default']) ) ? 
      $this->config['internal_biological_material']['taxon_code_default'] : '';

    $builder
      ->add('referentielTaxonFk', EntityType::class, [
        'class' => 'App:ReferentielTaxon',
        'choice_label' => $options['refTaxonLabel'],
        'query_builder' => function (EntityRepository $er) {
          return $er->createQueryBuilder('rt')
            ->orderBy('rt.taxname', 'ASC');
        },
        'multiple' => false,
        'expanded' => false,
        'required' => true,
        'placeholder' => 'Choose a Taxon',
        'data' => $this->er->getReference('App:ReferentielTaxon', 1)
      ])
      ->add('critereIdentificationVocFk', BaseVocType::class, array(
        'voc_parent' => 'critereIdentification',
        'expanded' => true,
        'label_attr' => array('class' => 'radio-inline'),
        'required' => false,          
      ))
      ->addEventSubscriber($this->addUserDate);
  }

  /**
   * {@inheritdoc} 
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $this->taxon_code_default = ( isset($this->config['internal_biological_material']['taxon_code_default']) ) ? 
      $this->config['internal_biological_material']['taxon_code_default'] : '';
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\EspeceIdentifiee',
      'refTaxonLabel' => 'taxname',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix():string {
    return 'bbees_e3sbundle_especeidentifiee';
  }
}
