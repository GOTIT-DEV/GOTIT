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

class SequenceAssembleeExtType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('collecteTypeahead', null, ['mapped' => false, 'attr' => ['class' => 'typeahead typeahead-collecte', 'data-target_id' => "bbees_e3sbundle_sequenceassembleeext_collecteId", 'name' => "where", 'placeholder' => "Collecte typeahead placeholder",  "maxlength" => "255"], 'required' => true, ])
                ->add('collecteId', HiddenType::class, array( 'mapped' => false, 'required' => true, ))  
                ->add('codeSqcAssExt')
                ->add('codeSqcAssExtAlignement')
                ->add('accessionNumberSqcAssExt')
                ->add('numIndividuSqcAssExt')
                ->add('taxonOrigineSqcAssExt')
                ->add('origineSqcAssExtVocFk', EntityType::class, array('class' => 'App:Voc', 
                   'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('voc')
                                ->where('voc.parent LIKE :parent')
                                ->setParameter('parent', 'origineSqcAssExt')
                                ->orderBy('voc.libelle', 'ASC');
                    }, 
                'choice_label' => 'code', 'multiple' => false, 'expanded' => false,'placeholder' => 'Choose a origineSqcAssExt')) 
                ->add('geneVocFk', EntityType::class, array('class' => 'App:Voc', 
                   'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('voc')
                                ->where('voc.parent LIKE :parent')
                                ->setParameter('parent', 'gene')
                                ->orderBy('voc.libelle', 'ASC');
                    }, 
                'choice_translation_domain' => true, 'choice_label' => 'code', 'multiple' => false, 'expanded' => false,'placeholder' => 'Choose a gene')) 
                ->add('statutSqcAssVocFk', EntityType::class, array('class' => 'App:Voc', 
                   'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('voc')
                                ->where('voc.parent LIKE :parent')
                                ->setParameter('parent', 'statutSqcAss')
                                ->orderBy('voc.libelle', 'ASC');
                    }, 
                'choice_label' => 'code', 'multiple' => false, 'expanded' => false,'placeholder' => 'Choose a statut')) 
                ->add('dateCreationSqcAssExt',DateType::class, array('widget' => 'text','format' => 'dd-MM-yyyy', 'required' => false, ))
                ->add('datePrecisionVocFk', EntityType::class, array('class' => 'App:Voc', 
                         'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                               ->where('voc.parent LIKE :parent')
                               ->setParameter('parent', 'datePrecision')
                               ->orderBy('voc.id', 'ASC');
                        }, 
                    'choice_translation_domain' => true, 'choice_label' => 'libelle', 'multiple' => false, 'expanded' => true, 'label_attr' => array('class' => 'radio-inline')))  
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
                        'entry_options' => array('label' => false, 'refTaxonLabel' => $options['refTaxonLabel'])
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
            'data_class' => 'App\Entity\SequenceAssembleeExt',
            'refTaxonLabel' => 'taxname',
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
