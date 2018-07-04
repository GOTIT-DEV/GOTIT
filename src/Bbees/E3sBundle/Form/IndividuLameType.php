<?php

namespace Bbees\E3sBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class IndividuLameType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('individuFk',EntityType::class, array('class' => 'BbeesE3sBundle:Individu','placeholder' => 'Choose an individu', 'choice_label' => 'code_ind_tri_morpho', 'multiple' => false, 'expanded' => false))
                ->add('codeLameColl')
                ->add('libelleLame')
                ->add('dateLame', DateType::class, array('widget' => 'text','format' => 'dd-MM-yyyy', 'required' => false, ))
                ->add('datePrecisionVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                         'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                               ->where('voc.parent LIKE :parent')
                               ->setParameter('parent', 'datePrecision')
                               ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_label' => 'libelle', 'multiple' => false, 'expanded' => true, 'label_attr' => array('class' => 'radio-inline')))
                ->add('nomDossierPhotos')
                ->add('commentaireLame')
                ->add('boiteFk',EntityType::class, array('class' => 'BbeesE3sBundle:Boite',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('boite')
                               ->leftJoin('BbeesE3sBundle:Voc', 'voc', 'WITH', 'boite.typeBoiteVocFk = voc.id')
                               ->where('voc.code LIKE :codetype')
                               ->setParameter('codetype', 'LAME')
                               ->orderBy('boite.libelleBoite', 'ASC');
                        }, 
                    'placeholder' => 'Choose a Box', 'choice_label' => 'code_boite', 'multiple' => false, 'expanded' => false, 'required' => false,))
                ->add('individuLameEstRealisePars', CollectionType::class , array(
        		'entry_type' => IndividuLameEstRealiseParEmbedType::class,
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
            'data_class' => 'Bbees\E3sBundle\Entity\IndividuLame'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_individulame';
    }


}
