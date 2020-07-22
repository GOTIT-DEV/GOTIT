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

use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\GeneType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class PcrType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $id = !is_null($builder->getData()->getAdnFk()) ? $builder->getData()->getAdnFk()->getId() : null;
        $builder->add('adnTypeahead', null, [
            'mapped' => false,
            'attr' => [
                'class' => 'typeahead typeahead-adn',
                'data-target_id' =>
                "bbees_e3sbundle_pcr_adnId",
                'name' => "where",
                'placeholder' =>
                "Adn typeahead placeholder",
                "maxlength" =>
                "255"
            ],
            'required' => true,
        ])
            ->add('adnId', HiddenType::class, array('mapped' => false, 'required' => true,))
            ->add('codePcr')
            ->add('numPcr')
            ->add('geneVocFk', GeneType::class)
            ->add('primerPcrStartVocFk', EntityType::class, array(
                'class' => 'App:Voc',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('voc')
                        ->where('voc.parent LIKE :parent')
                        ->setParameter('parent', 'primerPcrStart')
                        ->orderBy('voc.libelle', 'ASC');
                },
                'choice_label' => 'libelle',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Choose a primer start'
            ))
            ->add('primerPcrEndVocFk', EntityType::class, array(
                'class' => 'App:Voc',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('voc')
                        ->where('voc.parent LIKE :parent')
                        ->setParameter('parent', 'primerPcrEnd')
                        ->orderBy('voc.libelle', 'ASC');
                },
                'choice_label' => 'libelle',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Choose a primer end'
            ))
            ->add('datePrecisionVocFk', DatePrecisionType::class)
            ->add('datePcr', DateFormattedType::class)
            ->add('qualitePcrVocFk', EntityType::class, array(
                'class' => 'App:Voc',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('voc')
                        ->where('voc.parent LIKE :parent')
                        ->setParameter('parent', 'qualitePcr')
                        ->orderBy('voc.libelle', 'ASC');
                },
                'choice_translation_domain' => true,
                'choice_label' => 'libelle',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Choose a quality'
            ))
            ->add('specificiteVocFk', EntityType::class, array(
                'class' => 'App:Voc',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('voc')
                        ->where('voc.parent LIKE :parent')
                        ->setParameter('parent', 'specificite')
                        ->orderBy('voc.libelle', 'ASC');
                },
                'choice_translation_domain' => true,
                'choice_label' => 'libelle',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Choose a specificity'
            ))
            ->add('detailPcr')
            ->add('remarquePcr')
            ->add('pcrEstRealisePars', CollectionType::class, array(
                'entry_type' => PcrEstRealiseParEmbedType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__name__',
                'by_reference' => false,
                'entry_options' => array('label' => false)
            ))
            ->add('dateCre', DateTimeType::class, array(
                'required' => false,
                'widget' => 'single_text',
                'format' => 'Y-MM-dd HH:mm:ss',
                'html5' => false,
            ))
            ->add('dateMaj', DateTimeType::class, array(
                'required' => false,
                'widget' => 'single_text',
                'format' => 'Y-MM-dd HH:mm:ss',
                'html5' => false,
            ))
            ->add('userCre', HiddenType::class, array())
            ->add('userMaj', HiddenType::class, array());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Pcr'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_pcr';
    }
}
