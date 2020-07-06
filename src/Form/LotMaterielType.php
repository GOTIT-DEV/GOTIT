<?php

/*
 * This file is part of the E3sBundle.
 *
 * Authors : see information concerning authors of GOTIT project in file AUTHORS.md
 *
 * E3sBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * E3sBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with E3sBundle.  If not, see <https://www.gnu.org/licenses/>
 * 
 */

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
        $builder->add('collecteTypeahead', null, ['mapped' => false, 'attr' => ['class' => 'typeahead typeahead-collecte', 'data-target_id' => "bbees_e3sbundle_lotmateriel_collecteId", 'name' => "where", 'placeholder' => "Collecte typeahead placeholder",  "maxlength" => "255"], 'required' => true, ])
                ->add('collecteId', HiddenType::class, array( 'mapped' => false, 'required' => true, ))  
                ->add('codeLotMateriel')
                ->add('dateLotMateriel', DateType::class, array('widget' => 'text','format' => 'dd-MM-yyyy', 'required' => false, ))
                ->add('datePrecisionVocFk', EntityType::class, array('class' => 'App:Voc', 
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
                ->add('yeuxVocFk', EntityType::class, array('class' => 'App:Voc', 
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                                    ->where('voc.parent LIKE :parent')
                                    ->setParameter('parent', 'yeux')
                                    ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_translation_domain' => true, 'choice_label' => 'libelle', 'multiple' => false, 'expanded' => false, 'placeholder' => 'Choose a Eye'))
                ->add('pigmentationVocFk', EntityType::class, array('class' => 'App:Voc', 
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                                    ->where('voc.parent LIKE :parent')
                                    ->setParameter('parent', 'pigmentation')
                                    ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_translation_domain' => true, 'choice_label' => 'libelle', 'multiple' => false, 'expanded' => false,'placeholder' => 'Choose a Pigmentation'))
                ->add('aFaire', ChoiceType::class, array('choices'  => array('NO' => 0, 'YES' => 1,), 'required' => true,
                      'choice_translation_domain' => true, 'multiple' => false, 'expanded' => true, 'label_attr' => array('class' => 'radio-inline'), 
                    ))
                ->add('commentaireConseilSqc')
                ->add('commentaireLotMateriel')
                ->add('boiteFk',EntityType::class, array('class' => 'App:Boite',
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('boite')
                               ->leftJoin('App:Voc', 'voc', 'WITH', 'boite.typeBoiteVocFk = voc.id')
                               ->where('voc.code LIKE :codetype')
                               ->setParameter('codetype', 'LOT')
                               ->orderBy('LOWER(boite.codeBoite)', 'ASC');
                        }, 
                    'placeholder' => 'Choose a Box', 'choice_label' => 'codeBoite', 'multiple' => false, 'expanded' => false, 'required' => false,))
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
                        'required' => false, 
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
            'data_class' => 'App\Entity\LotMateriel'
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
