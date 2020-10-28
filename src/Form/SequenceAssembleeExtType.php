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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use App\Form\Type\SequenceStatusType;
use App\Form\Type\GeneType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\DateFormattedType;
use App\Form\EmbedTypes\SqcExtEstReferenceDansEmbedType;
use App\Form\EmbedTypes\SqcExtEstRealiseParEmbedType;
use App\Form\EmbedTypes\EspeceIdentifieeEmbedType;

class SequenceAssembleeExtType extends ActionFormType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('collecteTypeahead', null, [
            'mapped' => false,
            'attr' => [
                'class' => 'typeahead typeahead-collecte',
                'data-target_id' => "bbees_e3sbundle_sequenceassembleeext_collecteId",
                'name' => "where",
                'placeholder' => "Collecte typeahead placeholder",
                "maxlength" => "255"
            ],
            'required' => true,
        ])
            ->add('collecteId', HiddenType::class, [
                'mapped' => false,
                'required' => true,
            ])
            ->add('codeSqcAssExt')
            ->add('codeSqcAssExtAlignement')
            ->add('accessionNumberSqcAssExt')
            ->add('numIndividuSqcAssExt')
            ->add('taxonOrigineSqcAssExt')
            ->add('origineSqcAssExtVocFk', EntityType::class, [
                'class' => 'App:Voc',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('voc')
                        ->where('voc.parent LIKE :parent')
                        ->setParameter('parent', 'origineSqcAssExt')
                        ->orderBy('voc.libelle', 'ASC');
                },
                'choice_label' => 'code',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Choose a origineSqcAssExt'
            ])
            ->add('geneVocFk', GeneType::class)
            ->add('statutSqcAssVocFk', SequenceStatusType::class)
            ->add('datePrecisionVocFk', DatePrecisionType::class)
            ->add('dateCreationSqcAssExt', DateFormattedType::class)
            ->add('commentaireSqcAssExt')
            ->add('sqcExtEstRealisePars', CollectionType::class, array(
                'entry_type' => SqcExtEstRealiseParEmbedType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__name__',
                'by_reference' => false,
                'entry_options' => array('label' => false)
            ))
            ->add('especeIdentifiees', CollectionType::class, array(
                'entry_type' => EspeceIdentifieeEmbedType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__name__',
                'by_reference' => false,
                'entry_options' => array(
                    'label' => false,
                    'refTaxonLabel' => $options['refTaxonLabel']
                )
            ))
            ->add('sqcExtEstReferenceDanss', CollectionType::class, array(
                'entry_type' => SqcExtEstReferenceDansEmbedType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__name__',
                'by_reference' => false,
                'entry_options' => array('label' => false)
            ))
            ->addEventSubscriber($this->addUserDate);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
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
