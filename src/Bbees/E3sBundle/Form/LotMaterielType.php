<?php

namespace Bbees\E3sBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class LotMaterielType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('collecteFk',EntityType::class, array('class' => 'BbeesE3sBundle:Collecte',
                      'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('collecte')
                                    ->orderBy('collecte.codeCollecte', 'ASC');
                        },
                    'placeholder' => 'Choose a Collecte', 'choice_label' => 'code_collecte', 'multiple' => false, 'expanded' => false))
                ->add('codeLotMateriel')
                ->add('dateLotMateriel', DateType::class, array('widget' => 'text','format' => 'dd-MM-yyyy', 'required' => false, ))
                ->add('datePrecisionVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                         'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                               ->where('voc.parent LIKE :parent')
                               ->setParameter('parent', 'datePrecision')
                               ->orderBy('voc.id', 'ASC');
                        }, 
                    'choice_translation_domain' => true, 'choice_label' => 'libelle', 'multiple' => false, 'expanded' => true, 'label_attr' => array('class' => 'radio-inline')))
                ->add('lotMaterielEstRealisePars', CollectionType::class , array(
        		'entry_type' => LotMaterielEstRealiseParEmbedType::class,
        		'allow_add' => true,
        		'allow_delete' => true,
                        'prototype' => true,
                        'prototype_name' => '__name__',
        		'by_reference' => false,
                        'entry_options' => array('label' => false)
        	))
                ->add('especeIdentifiees', CollectionType::class , array(
        		'entry_type' => EspeceIdentifieeEmbedType::class,
        		'allow_add' => true,
        		'allow_delete' => true,
                        'prototype' => true,
                        'prototype_name' => '__name__',
        		'by_reference' => false,
                        'entry_options' => array('label' => false)
        	))
                ->add('yeuxVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                                    ->where('voc.parent LIKE :parent')
                                    ->setParameter('parent', 'yeux')
                                    ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_translation_domain' => true, 'choice_label' => 'libelle', 'multiple' => false, 'expanded' => false, 'placeholder' => 'Choose a Eye'))
                ->add('pigmentationVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                                    ->where('voc.parent LIKE :parent')
                                    ->setParameter('parent', 'pigmentation')
                                    ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_translation_domain' => true, 'choice_label' => 'libelle', 'multiple' => false, 'expanded' => false,'placeholder' => 'Choose a Pigmentation'))
                ->add('aFaire', ChoiceType::class, array('choices'  => array('No' => 0, 'Yes' => 1,), 'required' => true,
                      'multiple' => false, 'expanded' => true, 'label_attr' => array('class' => 'radio-inline'), 
                    ))
                ->add('commentaireConseilSqc')
                ->add('commentaireLotMateriel')
                ->add('boiteFk',EntityType::class, array('class' => 'BbeesE3sBundle:Boite',
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('boite')
                               ->leftJoin('BbeesE3sBundle:Voc', 'voc', 'WITH', 'boite.typeBoiteVocFk = voc.id')
                               ->where('voc.code LIKE :codetype')
                               ->setParameter('codetype', 'LOT')
                               ->orderBy('boite.libelleBoite', 'ASC');
                        }, 
                    'placeholder' => 'Choose a Box', 'choice_label' => 'code_boite', 'multiple' => false, 'expanded' => false, 'required' => false,))
                ->add('compositionLotMateriels', CollectionType::class , array(
        		'entry_type' => CompositionLotMaterielEmbedType::class,
        		'allow_add' => true,
        		'allow_delete' => true,
                        'prototype' => true,
                        'prototype_name' => '__name__',
        		'by_reference' => false,
                        'entry_options' => array('label' => false)
        	))     
                ->add('lotEstPublieDanss', CollectionType::class , array(
        		'entry_type' => LotEstPublieDansEmbedType::class,
        		'allow_add' => true,
        		'allow_delete' => true,
                        'prototype' => true,
                        'prototype_name' => '__name__',
        		'by_reference' => false,
                        'entry_options' => array('label' => false)
        	))   
                ->add('dateCre', DateTimeType::class, array( 'required' => false, 'widget' => 'single_text', 'format' => 'Y-MM-dd HH:mm:ss', 'html5' => false,  ))
                ->add('dateMaj', DateTimeType::class, array( 'required' => false,  'widget' => 'single_text', 'format' => 'Y-MM-dd HH:mm:ss', 'html5' => false, ))
                ->add('userCre', HiddenType::class, array())
                ->add('userMaj', HiddenType::class, array())
                                ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bbees\E3sBundle\Entity\LotMateriel'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_lotmateriel';
    }


}
