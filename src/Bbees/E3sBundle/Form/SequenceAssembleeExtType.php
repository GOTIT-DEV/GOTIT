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

class SequenceAssembleeExtType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('collecteFk',EntityType::class, array('class' => 'BbeesE3sBundle:Collecte','placeholder' => 'Choose a Collecte', 'choice_label' => 'code_collecte', 'multiple' => false, 'expanded' => false))
                ->add('codeSqcAssExt')
                ->add('codeSqcAssExtAlignement')
                ->add('accessionNumberSqcAssExt')
                ->add('numIndividuSqcAssExt')
                ->add('taxonOrigineSqcAssExt')
                ->add('origineSqcAssExtVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                   'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('voc')
                                ->where('voc.parent LIKE :parent')
                                ->setParameter('parent', 'origineSqcAssExt')
                                ->orderBy('voc.libelle', 'ASC');
                    }, 
                'choice_label' => 'code', 'multiple' => false, 'expanded' => false,'placeholder' => 'Choose a origineSqcAssExt')) 
                ->add('geneVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                   'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('voc')
                                ->where('voc.parent LIKE :parent')
                                ->setParameter('parent', 'gene')
                                ->orderBy('voc.libelle', 'ASC');
                    }, 
                'choice_label' => 'code', 'multiple' => false, 'expanded' => false,'placeholder' => 'Choose a gene')) 
                ->add('statutSqcAssVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                   'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('voc')
                                ->where('voc.parent LIKE :parent')
                                ->setParameter('parent', 'statutSqcAss')
                                ->orderBy('voc.libelle', 'ASC');
                    }, 
                'choice_label' => 'code', 'multiple' => false, 'expanded' => false,'placeholder' => 'Choose a statut')) 
                ->add('dateCreationSqcAssExt',DateType::class, array('widget' => 'text','format' => 'dd-MM-yyyy', 'required' => false, ))
                ->add('datePrecisionVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                         'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                               ->where('voc.parent LIKE :parent')
                               ->setParameter('parent', 'datePrecision')
                               ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_label' => 'libelle', 'multiple' => false, 'expanded' => true, 'label_attr' => array('class' => 'radio-inline')))  
                ->add('commentaireSqcAssExt')
                ->add('sqcExtEstRealisePars', CollectionType::class , array(
                        'entry_type' => SqcExtEstRealiseParEmbedType::class,
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
                        'entry_options' => array('label' => false, 'refTaxonLabel' => 'codeTaxon')
                )) 
                ->add('sqcExtEstReferenceDanss', CollectionType::class , array(
                        'entry_type' => SqcExtEstReferenceDansEmbedType::class,
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
            'data_class' => 'Bbees\E3sBundle\Entity\SequenceAssembleeExt'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_sequenceassembleeext';
    }


}
