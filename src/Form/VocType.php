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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Form\Type\UppercaseType;
use App\Form\ActionFormType;

class VocType extends ActionFormType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', UppercaseType::class)
            ->add('libelle', UppercaseType::class)
            ->add('parent', ChoiceType::class, array(
                'choices' => [
                    'vocParent.codeCollection' => 'codeCollection',
                    'vocParent.datePrecision' => 'datePrecision,',
                    'vocParent.fixateur' => 'fixateur',
                    'vocParent.gene' => 'gene',
                    'vocParent.habitatType' => 'habitatType',
                    'vocParent.leg' => 'leg',
                    'vocParent.methodeExtractionAdn' => 'methodeExtractionAdn',
                    'vocParent.methodeMotu' => 'methodeMotu',
                    'vocParent.nbIndividus' => 'nbIndividus',
                    'vocParent.origineSqcAssExt' => 'origineSqcAssExt',
                    'vocParent.pigmentation' => 'pigmentation',
                    'vocParent.pointAcces' => 'pointAcces',
                    'vocParent.precisionLatLong' => 'precisionLatLong',
                    'vocParent.primerChromato' => 'primerChromato',
                    'vocParent.primerPcrEnd' => 'primerPcrEnd',
                    'vocParent.primerPcrStart' => 'primerPcrStart',
                    'vocParent.qualiteAdn' => 'qualiteAdn',
                    'vocParent.qualiteChromato' => 'qualiteChromato',
                    'vocParent.qualitePcr' => 'qualitePcr',
                    'vocParent.samplingMethod' => 'samplingMethod',
                    'vocParent.specificite' => 'specificite',
                    'vocParent.statutSqcAss' => 'statutSqcAss',
                    'vocParent.typeBoite' => 'typeBoite',
                    'vocParent.typeCollection' => 'typeCollection',
                    'vocParent.typeIndividu' => 'typeIndividu',
                    'vocParent.typeMateriel' => 'typeMateriel',
                    'vocParent.yeux' => 'yeux',
                ],
                'placeholder' => 'Choose a Parent',
                'required' => true,
                'choice_translation_domain' => true,
                'multiple' => false,
                'expanded' => false,
            ))
            ->add('commentaire')
            ->addEventSubscriber($this->addUserDate);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Voc'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_voc';
    }
}
