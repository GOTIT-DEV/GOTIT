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

use App\Form\ActionFormType;
use Doctrine\ORM\EntityRepository;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\ExtractionMethodType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Form\EmbedTypes\AdnEstRealiseParEmbedType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdnType extends ActionFormType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('individuTypeahead', null, [
                'mapped' => false,
                'attr' => [
                    'class' => 'typeahead typeahead-individu',
                    'data-target_id' => "bbees_e3sbundle_adn_individuId",
                    'name' => "where",
                    'placeholder' => "Individu typeahead placeholder",
                    "maxlength" => "255",
                ],
                'required' => true,
                'disabled' => $this->canEditAdminOnly($options)
            ])
            ->add('individuId', HiddenType::class, array(
                'mapped' => false,
                'required' => true,
            ))
            ->add('codeAdn', null, [
                'disabled' => $this->canEditAdminOnly($options)
            ])
            ->add('datePrecisionVocFk', DatePrecisionType::class)
            ->add('dateAdn', DateFormattedType::class)
            ->add('methodeExtractionAdnVocFk', ExtractionMethodType::class)
            ->add('concentrationNgMicrolitre', NumberType::class, array(
                'scale' => 4,
                'required' => false
            ))
            ->add('commentaireAdn')
            ->add('qualiteAdnVocFk', EntityType::class, array(
                'class' => 'App:Voc',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('voc')
                        ->where('voc.parent LIKE :parent')
                        ->setParameter('parent', 'qualiteAdn')
                        ->orderBy('voc.libelle', 'ASC');
                },
                'choice_translation_domain' => true,
                'choice_label' => 'libelle',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Choose a quality'
            ))
            ->add('boiteFk', EntityType::class, array(
                'class' => 'App:Boite',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('boite')
                        ->leftJoin('App:Voc', 'voc', 'WITH', 'boite.typeBoiteVocFk = voc.id')
                        ->where('voc.code LIKE :codetype')
                        ->setParameter('codetype', 'ADN')
                        ->orderBy('LOWER(boite.codeBoite)', 'ASC');
                },
                'placeholder' => 'Choose a Box',
                'choice_label' => 'codeBoite',
                'multiple' => false,
                'expanded' => false,
                'required' => false,
            ))
            ->add('adnEstRealisePars', CollectionType::class, [
                'entry_type' => AdnEstRealiseParEmbedType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__name__',
                'by_reference' => false,
                'entry_options' => array('label' => false),
                'attr' => [
                    "data-allow-new" => true,
                    "data-modal-controller" => 'App\\Controller\\Core\\PersonneController::newmodalAction'
                ]
            ])
            ->addEventSubscriber($this->addUserDate);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Adn'
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
