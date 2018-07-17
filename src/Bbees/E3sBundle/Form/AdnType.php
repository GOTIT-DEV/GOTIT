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
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class AdnType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('individuFk',EntityType::class, array('class' => 'BbeesE3sBundle:Individu',
                         'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('ind')
                               ->where('ind.codeIndBiomol IS NOT NULL')
                               ->orderBy('ind.codeIndBiomol', 'ASC');
                        }, 
                         'placeholder' => 'Choose an individu', 'choice_label' => 'code_ind_biomol', 'multiple' => false, 'expanded' => false))
                ->add('codeAdn')
                ->add('dateAdn', DateType::class, array('widget' => 'text','format' => 'dd-MM-yyyy', 'required' => false, ))
                ->add('datePrecisionVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                         'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                               ->where('voc.parent LIKE :parent')
                               ->setParameter('parent', 'datePrecision')
                               ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_label' => 'libelle', 'multiple' => false, 'expanded' => true, 'label_attr' => array('class' => 'radio-inline')))
                ->add('methodeExtractionAdnVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                                    ->where('voc.parent LIKE :parent')
                                    ->setParameter('parent', 'methodeExtractionAdn')
                                    ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_label' => 'libelle', 'multiple' => false, 'expanded' => false,'placeholder' => 'Choose a method'))
                ->add('concentrationNgMicrolitre', NumberType::class,array( 'scale' => 4 ))
                ->add('commentaireAdn')
                ->add('qualiteAdnVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                                    ->where('voc.parent LIKE :parent')
                                    ->setParameter('parent', 'qualiteAdn')
                                    ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_label' => 'libelle', 'multiple' => false, 'expanded' => false,'placeholder' => 'Choose a quality'))             
                ->add('boiteFk',EntityType::class, array('class' => 'BbeesE3sBundle:Boite',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('boite')
                               ->leftJoin('BbeesE3sBundle:Voc', 'voc', 'WITH', 'boite.typeBoiteVocFk = voc.id')
                               ->where('voc.code LIKE :codetype')
                               ->setParameter('codetype', 'ADN')
                               ->orderBy('boite.libelleBoite', 'ASC');
                        }, 
                    'placeholder' => 'Choose a Box', 'choice_label' => 'code_boite', 'multiple' => false, 'expanded' => false, 'required' => false,))
                ->add('adnEstRealisePars', CollectionType::class , array(
        		'entry_type' => AdnEstRealiseParEmbedType::class,
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
            'data_class' => 'Bbees\E3sBundle\Entity\Adn'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_adn';
    }


}
