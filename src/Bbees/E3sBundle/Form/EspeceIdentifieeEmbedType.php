<?php

namespace Bbees\E3sBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EspeceIdentifieeEmbedType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('referentielTaxonFk', EntityType::class, array('class' => 'BbeesE3sBundle:ReferentielTaxon', 
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('rt')
                                    ->orderBy('rt.taxname', 'ASC');
                            }, 
                        'choice_label' => $options['refTaxonLabel'], 'multiple' => false, 'expanded' => false, 'required' => true,'placeholder' => 'Choose a Taxon')
                        )
                ->add('critereIdentificationVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                   'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('voc')
                                ->where('voc.parent LIKE :parent')
                                ->setParameter('parent', 'critereIdentification')
                                ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_translation_domain' => true, 'choice_label' => 'libelle', 'multiple' => false, 'expanded' => true, 'label_attr' => array('class' => 'radio-inline'), 'required' => true,)
                        )
                ->add('dateIdentification', DateType::class, array('widget' => 'text','format' => 'dd-MM-yyyy', 'required' => false, ))
                ->add('datePrecisionVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                   'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('voc')
                                ->where('voc.parent LIKE :parent')
                                ->setParameter('parent', 'datePrecision')
                                ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_translation_domain' => true, 'choice_label' => 'libelle', 'multiple' => false, 'expanded' => true, 'label_attr' => array('class' => 'radio-inline'), 'required' => true,)
                    )
                ->add('commentaireEspId')
                ->add('estIdentifiePars', CollectionType::class , array(
        		'entry_type' => EstIdentifieParEmbedType::class,
        		'allow_add' => true,
        		'allow_delete' => true,
                        'prototype' => true,
                        'prototype_name' => '__name__',
        		'by_reference' => false,
                        'entry_options' => array('label' => false)
        	))
                ->add('dateCre', DateTimeType::class, array( 'required' => false, 'widget' => 'single_text', 'format' => 'Y-MM-dd HH:mm:ss', 'html5' => false, 'data' =>  new \DateTime("now"), 'label' => false, 'attr'=>array('style'=>'display:none;')))
                ->add('dateMaj', DateTimeType::class, array( 'required' => false,  'widget' => 'single_text', 'format' => 'Y-MM-dd HH:mm:ss', 'html5' => false, 'data' =>  new \DateTime("now"), 'label' => false, 'attr'=>array('style'=>'display:none;')))
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
            'data_class' => 'Bbees\E3sBundle\Entity\EspeceIdentifiee',
            'refTaxonLabel' => 'taxname',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_especeidentifiee';
    }


}
