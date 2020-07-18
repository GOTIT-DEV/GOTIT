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

use App\Form\Type\SpecimenVocType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class IndividuType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('lotmaterielTypeahead', null, [
            'mapped' => false,
            'attr' => [
                'class' => 'typeahead typeahead-lotmateriel',
                'data-target_id' => "bbees_e3sbundle_individu_lotmaterielId",
                'name' => "where",
                'placeholder' => "Lotmateriel typeahead placeholder",
                "maxlength" => "255"
            ],
            'required' => true,
        ])
            ->add('lotmaterielId', HiddenType::class, array(
                'mapped' => false,
                'required' => true,
            ))
            ->add('codeTube')
            ->add('codeIndTriMorpho')
            ->add('typeIndividuVocFk', SpecimenVocType::class)
            ->add('numIndBiomol')
            ->add('codeIndBiomol')
            ->add('commentaireInd')
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
            ->add('userCre', HiddenType::class, array())
            ->add('userMaj', HiddenType::class, array());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Individu',
            'refTaxonLabel' => 'taxname',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_individu';
    }
}
