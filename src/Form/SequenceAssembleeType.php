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

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use App\Form\Type\SequenceStatusType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\DateFormattedType;
use App\Form\EmbedTypes\SqcEstPublieDansEmbedType;
use App\Form\EmbedTypes\SequenceAssembleeEstRealiseParEmbedType;
use App\Form\EmbedTypes\EstAligneEtTraiteEmbedType;
use App\Form\EmbedTypes\EspeceIdentifieeEmbedType;

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
                ->add('dateCreationSqcAss', DateFormattedType::class)
                ->add('datePrecisionVocFk', DatePrecisionType::class)
                ->add('statutSqcAssVocFk', SequenceStatusType::class) 
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
                        'required' => false, 
                        'entry_options' => array('label' => false,)
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
            'data_class' => 'App\Entity\SequenceAssemblee',
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
