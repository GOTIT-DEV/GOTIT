<?php

namespace App\Form;

use App\Form\ActionFormType;
use App\Form\EmbedTypes\CompositionLotMaterielEmbedType;
use App\Form\EmbedTypes\EspeceIdentifieeEmbedType;
use App\Form\EmbedTypes\LotEstPublieDansEmbedType;
use App\Form\EmbedTypes\LotMaterielEstRealiseParEmbedType;
use App\Form\Enums\Action;
use App\Form\Type\BaseVocType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\SearchableSelectType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
//
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


class LotMaterielType extends ActionFormType {
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $sampling = $builder->getData()->getCollecteFk();
    $this->taxon_name_default = ( isset($this->config['internal_biological_material']['taxon_name_default']) ) ? 
            $this->config['internal_biological_material']['taxon_name_default'] : '';
    $this->taxon_code_default = ( isset($this->config['internal_biological_material']['taxon_code_default']) ) ? 
            $this->config['internal_biological_material']['taxon_code_default'] : '';
    $this->eyes_voc_fk = ( isset($this->config['internal_biological_material']['eyes_voc_fk']) ) ? 
            $this->config['internal_biological_material']['eyes_voc_fk'] : 1;
    $this->pigmentation_voc_fk = ( isset($this->config['internal_biological_material']['pigmentation_voc_fk']) ) ? 
            $this->config['internal_biological_material']['pigmentation_voc_fk'] : 1;

    $builder
      ->add('collecteFk', SearchableSelectType::class, [
        'class' => 'App:Collecte',
        'choice_label' => 'codeCollecte',
        'placeholder' => $this->translator->trans("Collecte typeahead placeholder"),
        'disabled' => $this->canEditAdminOnly($options),
        'attr' => [
          'readonly' => $sampling != null,
        ],
      ])
      ->add('codeLotMateriel', EntityCodeType::class, [
        'attr' => [
          'readonly' => $options['action_type'] == Action::create->value,
        ],
        'disabled' => $this->canEditAdminOnly($options),
      ])
      ->add('datePrecisionVocFk', DatePrecisionType::class)
      ->add('dateLotMateriel', DateFormattedType::class)
      ->add('lotMaterielEstRealisePars', CollectionType::class, array(
        'entry_type' => LotMaterielEstRealiseParEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array('label' => false),
        'attr' => [
          "data-allow-new" => true,
          "data-modal-controller" => 'App\\Controller\\Core\\PersonneController::newmodalAction',
        ],
        ));
    
      if ($this->taxon_code_default != '' && $this->taxon_name_default != '')   { 
        $builder
        ->add('especeIdentifiees_taxon_default_name', HiddenType::class,  [
            'mapped' => false,
            'attr' => ['class' => 'hidden-field', 'value' => $this->taxon_name_default]
          ]) 
        ->add('especeIdentifiees_taxon_default_code', HiddenType::class,  [
            'mapped' => false,
            'attr' => ['class' => 'hidden-field', 'value' => $this->taxon_code_default]
          ]) 
        ->add('especeIdentifiees', CollectionType::class, array(
        'entry_type' => EspeceIdentifieeEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array('label' => false, 'required' => true, 'refTaxonLabel' => $options['refTaxonLabel']),
        'required' => false,
        )) ;
      } else {
        $builder
        ->add('especeIdentifiees', CollectionType::class, array(
        'entry_type' => EspeceIdentifieeEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array('label' => false, 'refTaxonLabel' => $options['refTaxonLabel']),
        'required' => true,
        )) ;
      } 
           
      if($this->eyes_voc_fk ){
        $builder
        ->add('yeuxVocFk', BaseVocType::class, array(
          'voc_parent' => 'yeux',
          'placeholder' => 'Choose a Eye',
          'required' => false,
        ))  ;        
      }
      if($this->pigmentation_voc_fk ){
        $builder
        ->add('pigmentationVocFk', BaseVocType::class, array(
          'voc_parent' => 'pigmentation',
          'placeholder' => 'Choose a Pigmentation',
          'required' => false,
        ))  ;        
      }
      $builder
      ->add('aFaire', ChoiceType::class, array(
        'choices' => array('NO' => 0, 'YES' => 1),
        'required' => true,
        'choice_translation_domain' => true,
        'multiple' => false,
        'expanded' => true,
        'label_attr' => array('class' => 'radio-inline'),
      ))
      ->add('commentaireConseilSqc')
      ->add('commentaireLotMateriel')
                
      ->add('boites', EntityType::class, [
            'class'        => 'App\\Entity\\Boite',
            'choice_label' => 'codeBoite',
            'label'        => 'codeBoite label',
            'expanded'     => false,
            'multiple'     => true,
            'required' => false,
        ])   
               
      ->add('compositionLotMateriels', CollectionType::class, array(
        'entry_type' => CompositionLotMaterielEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'entry_options' => array('label' => false),
      ))
      ->add('lotEstPublieDanss', CollectionType::class, array(
        'entry_type' => LotEstPublieDansEmbedType::class,
        'allow_add' => true,
        'allow_delete' => true,
        'prototype' => true,
        'prototype_name' => '__name__',
        'by_reference' => false,
        'required' => false,
        'entry_options' => array('label' => false),
      ))
      ->addEventSubscriber($this->addUserDate);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\LotMateriel',
      'refTaxonLabel' => 'taxname',
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix():string {
    return 'bbees_e3sbundle_lotmateriel';
  }
}
