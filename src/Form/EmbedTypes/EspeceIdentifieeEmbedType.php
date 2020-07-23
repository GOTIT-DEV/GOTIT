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

namespace App\Form\EmbedTypes;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use App\Form\Type\DatePrecisionType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\TaxnameType;
use App\Form\UserDateTraceType;

class EspeceIdentifieeEmbedType extends UserDateTraceType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('referentielTaxonFk', TaxnameType::class, [
                'choice_label' => $options['refTaxonLabel']
            ])
            ->add('critereIdentificationVocFk', EntityType::class, array(
                'class' => 'App:Voc',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('voc')
                        ->where('voc.parent LIKE :parent')
                        ->setParameter('parent', 'critereIdentification')
                        ->orderBy('voc.libelle', 'ASC');
                },
                'choice_translation_domain' => true,
                'choice_label' => 'libelle',
                'multiple' => false,
                'expanded' => true,
                'attr' => ["class" => "stacked"],
                'label_attr' => array('class' => 'radio-inline'),
                'required' => true,
            ))
            ->add('dateIdentification', DateFormattedType::class)
            ->add('datePrecisionVocFk', DatePrecisionType::class)
            ->add('typeMaterielVocFk', EntityType::class, array(
                'class' => 'App:Voc',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('voc')
                        ->where('voc.parent LIKE :parent')
                        ->setParameter('parent', 'typeMaterial')
                        ->orderBy('voc.id', 'ASC');
                },
                'choice_translation_domain' => true,
                'choice_label' => 'libelle',
                'multiple' => false,
                'expanded' => true,
                'label_attr' => array('class' => 'radio-inline'),
                'required' => true,
            ))
            ->add('commentaireEspId')
            ->add('estIdentifiePars', CollectionType::class, array(
                'entry_type' => EstIdentifieParEmbedType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__name_inner__',
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
            'data_class' => 'App\Entity\EspeceIdentifiee',
            'refTaxonLabel' => 'taxname',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_especeidentifiee';
    }
}
