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

class IndividuType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('lotMaterielFk',EntityType::class, array('class' => 'BbeesE3sBundle:LotMateriel','placeholder' => 'Choose a Lot Materiel', 'choice_label' => 'code_lot_materiel', 'multiple' => false, 'expanded' => false))
                ->add('codeTube')
                ->add('codeIndTriMorpho')
                ->add('typeIndividuVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                                    ->where('voc.parent LIKE :parent')
                                    ->setParameter('parent', 'typeIndividu')
                                    ->orderBy('voc.libelle', 'ASC');
                        },
                    'choice_translation_domain' => true, 'choice_label' => 'libelle', 'multiple' => false, 'expanded' => false, 'placeholder' => 'Choose a Type'))
                ->add('numIndBiomol')
                ->add('codeIndBiomol')
                ->add('commentaireInd')
                ->add('especeIdentifiees', CollectionType::class , array(
        		'entry_type' => EspeceIdentifieeEmbedType::class,
        		'allow_add' => true,
        		'allow_delete' => true,
                        'prototype' => true,
                        'prototype_name' => '__name__',
        		'by_reference' => false,
                        'entry_options' => array('label' => false, 'refTaxonLabel' => $options['refTaxonLabel'])
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
            'data_class' => 'Bbees\E3sBundle\Entity\Individu',
            'refTaxonLabel' => 'taxname',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_individu';
    }


}
