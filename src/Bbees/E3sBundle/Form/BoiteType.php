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

class BoiteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('codeBoite')
                ->add('libelleBoite')
                ->add('libelleCollection')
                ->add('commentaireBoite')
                ->add('typeCollectionVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                                    ->where('voc.parent LIKE :parent')
                                    ->setParameter('parent', 'typeCollection')
                                    ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_label' => 'code', 'multiple' => false, 'expanded' => false,'placeholder' => 'Choose a typeCollection')) 
                ->add('codeCollectionVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                                    ->where('voc.parent LIKE :parent')
                                    ->setParameter('parent', 'codeCollection')
                                    ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_label' => 'code', 'multiple' => false, 'expanded' => false,'placeholder' => 'Choose a codeCollection')) 
                ->add('typeBoiteVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                                    ->where('voc.parent LIKE :parent')
                                    ->setParameter('parent', 'typeBoite')
                                    ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_label' => 'code', 'multiple' => false, 'expanded' => false,'placeholder' => 'Choose a typeBoite'))
                ->add('adns', CollectionType::class , array(
                        'entry_type' => AdnEmbedType::class,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'prototype' => true,
                        'prototype_name' => '__name__',
                        'by_reference' => false,
                        'entry_options' => array('label' => false)
                ))
                ->add('lotMateriels', CollectionType::class , array(
                        'entry_type' => LotMaterielEmbedType::class,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'prototype' => true,
                        'prototype_name' => '__name__',
                        'by_reference' => false,
                        'entry_options' => array('label' => false)
                )) 
                ->add('individuLames', CollectionType::class , array(
                        'entry_type' => IndividuLameEmbedType::class,
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
            'data_class' => 'Bbees\E3sBundle\Entity\Boite'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_boite';
    }


}
