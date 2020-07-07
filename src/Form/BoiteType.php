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

namespace App\Form;

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
                ->add('commentaireBoite')
                ->add('typeCollectionVocFk', EntityType::class, array('class' => 'App:Voc', 
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                                    ->where('voc.parent LIKE :parent')
                                    ->setParameter('parent', 'typeCollection')
                                    ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_translation_domain' => true, 'choice_label' => 'libelle', 'multiple' => false, 'expanded' => false,'placeholder' => 'Choose a typeCollection')) 
                ->add('codeCollectionVocFk', EntityType::class, array('class' => 'App:Voc', 
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                                    ->where('voc.parent LIKE :parent')
                                    ->setParameter('parent', 'codeCollection')
                                    ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_label' => 'libelle', 'multiple' => false, 'expanded' => false,'placeholder' => 'Choose a Collection')) 
                ->add('typeBoiteVocFk', EntityType::class, array('class' => 'App:Voc', 
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
                        'required' => false,
                        'entry_options' => array('label' => false)
                ))
                ->add('lotMateriels', CollectionType::class , array(
                        'entry_type' => LotMaterielEmbedType::class,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'prototype' => true,
                        'prototype_name' => '__name__',
                        'by_reference' => false,
                        'required' => false,
                        'entry_options' => array('label' => false)
                )) 
                ->add('individuLames', CollectionType::class , array(
                        'entry_type' => IndividuLameEmbedType::class,
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
            'data_class' => 'App\Entity\Boite'
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
