<?php

namespace Bbees\E3sBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Bbees\E3sBundle\Form\APourSamplingMethodEmbedType;
use Bbees\E3sBundle\Form\APourSamplingMethodType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class CollecteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('stationFk',EntityType::class, array('class' => 'BbeesE3sBundle:Station','placeholder' => 'Choose a Station', 'choice_label' => 'code_station', 'multiple' => false, 'expanded' => false))
                ->add('codeCollecte')
                ->add('dateCollecte', DateType::class, array('widget' => 'text','format' => 'dd-MM-yyyy', 'required' => false, ))
                ->add('datePrecisionVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                                    ->where('voc.parent LIKE :parent')
                                    ->setParameter('parent', 'datePrecision')
                                    ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_translation_domain' => true, 'choice_label' => 'libelle', 'multiple' => false, 'expanded' => true, 'label_attr' => array('class' => 'radio-inline')))
                ->add('aPourSamplingMethods', CollectionType::class , array(
        		'entry_type' => APourSamplingMethodEmbedType::class,
        		'allow_add' => true,
        		'allow_delete' => true,
                        'prototype' => true,
                        'prototype_name' => '__name__',
        		'by_reference' => false,
                        'entry_options' => array('label' => false)
        	))
                ->add('aPourFixateurs', CollectionType::class , array(
        		'entry_type' => APourFixateurEmbedType::class,
        		'allow_add' => true,
        		'allow_delete' => true,
                        'prototype' => true,
                        'prototype_name' => '__name__',
        		'by_reference' => false,
                        'entry_options' => array('label' => false)
        	))
                ->add('estFinancePars', CollectionType::class , array(
        		'entry_type' => EstFinanceParEmbedType::class,
        		'allow_add' => true,
        		'allow_delete' => true,
                        'prototype' => true,
                        'prototype_name' => '__name__',
        		'by_reference' => false,
                        'entry_options' => array('label' => false)
        	))
                ->add('estEffectuePars', CollectionType::class , array(
        		'entry_type' => EstEffectueParEmbedType::class,
        		'allow_add' => true,
        		'allow_delete' => true,
                        'prototype' => true,
                        'prototype_name' => '__name__',
        		'by_reference' => false,
                        'entry_options' => array('label' => false)
        	))
                ->add('aCiblers', CollectionType::class , array(
        		'entry_type' => ACiblerEmbedType::class,
        		'allow_add' => true,
        		'allow_delete' => true,
                        'prototype' => true,
                        'prototype_name' => '__name__',
        		'by_reference' => false,
                        'entry_options' => array('label' => false)
        	))
                ->add('dureeEchantillonnageMn')->add('temperatureC')->add('conductiviteMicroSieCm')
                ->add('aFaire', ChoiceType::class, array('choices'  => array('No' => 0, 'Yes' => 1,), 'required' => true,
                               'multiple' => false, 'expanded' => true, 'label_attr' => array('class' => 'radio-inline'), 
                    ))
                ->add('commentaireCollecte')
                ->add('legVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                         'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                               ->where('voc.parent LIKE :parent')
                               ->setParameter('parent', 'leg')
                               ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_translation_domain' => true, 'choice_label' => 'libelle', 'multiple' => false, 'expanded' => true, 'label_attr' => array('class' => 'radio-inline')))
                ->add('dateCre', DateTimeType::class, array( 'required' => false, 'widget' => 'single_text', 'format' => 'Y-MM-dd HH:mm:ss', 'html5' => false,  ))
                ->add('dateMaj', DateTimeType::class, array( 'required' => false,  'widget' => 'single_text', 'format' => 'Y-MM-dd HH:mm:ss', 'html5' => false, ))
                ->add('userCre', HiddenType::class, array())
                ->add('userMaj', HiddenType::class, array());
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bbees\E3sBundle\Entity\Collecte', 
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_collecte';
    }


}
