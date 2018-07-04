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

class SequenceAssembleeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('codeSqcAss')
                ->add('accessionNumber')
                ->add('codeSqcAlignement')
                ->add('commentaireSqcAss')
                ->add('dateCreationSqcAss',DateType::class, array('widget' => 'text','format' => 'dd-MM-yyyy', 'required' => false, ))
                ->add('datePrecisionVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                         'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                               ->where('voc.parent LIKE :parent')
                               ->setParameter('parent', 'datePrecision')
                               ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_label' => 'libelle', 'multiple' => false, 'expanded' => true, 'label_attr' => array('class' => 'radio-inline')))
                ->add('statutSqcAssVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                                    ->where('voc.parent LIKE :parent')
                                    ->setParameter('parent', 'statutSqcAss')
                                    ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_label' => 'code', 'multiple' => false, 'expanded' => false,'placeholder' => 'Choose a status')) 
                ->add('estAligneEtTraites', CollectionType::class , array(
        		'entry_type' => EstAligneEtTraiteEmbedType::class,
        		'allow_add' => true,
        		'allow_delete' => true,
                        'prototype' => true,
                        'prototype_name' => '__name__',
        		'by_reference' => false,
                        'entry_options' => array('label' => false, 'geneVocFk' => $options['geneVocFk'], 'individuFk' => $options['individuFk'])
        	))                
                ->add('sequenceAssembleeEstRealisePars', CollectionType::class , array(
        		'entry_type' => SequenceAssembleeEstRealiseParEmbedType::class,
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
                ->add('sqcEstPublieDanss', CollectionType::class , array(
        		'entry_type' => SqcEstPublieDansEmbedType::class,
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
            'data_class' => 'Bbees\E3sBundle\Entity\SequenceAssemblee',
            'geneVocFk' => '',
            'individuFk' => '',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_sequenceassemblee';
    }


}
