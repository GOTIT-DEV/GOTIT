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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use App\Form\Type\UppercaseType;
use App\Form\Type\CountryVocType;
use App\Form\ActionFormType;

class StationType extends ActionFormType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codeStation', UppercaseType::class, [
                'attr' => [
                    'readonly' => $this->canEditAdminOnly($options),
                ],
            ])
            ->add('nomStation', UppercaseType::class)
            ->add('infoDescription')
            ->add('paysFk', CountryVocType::class)
            ->add('communeFk', EntityType::class, array(
                'class' => 'App:Commune',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('commune')
                        ->orderBy('commune.codeCommune', 'ASC');
                },
                'choice_label' => 'codeCommune',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Choose a Commune'
            ))
            ->add('habitatTypeVocFk', EntityType::class, array(
                'class' => 'App:Voc',
                'placeholder' => 'Choose an Habitat Type',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('voc')
                        ->where('voc.parent LIKE :parent')
                        ->setParameter('parent', 'habitatType')
                        ->orderBy('voc.libelle', 'ASC');
                },
                'choice_translation_domain' => true,
                'choice_label' => 'libelle',
                'multiple' => false,
                'expanded' => false
            ))
            ->add('pointAccesVocFk', EntityType::class, array(
                'class' => 'App:Voc',
                'placeholder' => 'Choose an Access Point',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('voc')
                        ->where('voc.parent LIKE :parent')
                        ->setParameter('parent', 'pointAcces')
                        ->orderBy('voc.libelle', 'ASC');
                },
                'choice_translation_domain' => true,
                'choice_label' => 'libelle',
                'multiple' => false,
                'expanded' => false
            ))
            ->add('latDegDec', NumberType::class, array(
                'required' => true,
                'scale' => 6
            ))
            ->add('longDegDec', NumberType::class, array(
                'required' => true,
                'scale' => 6
            ))
            ->add('precisionLatLongVocFk', EntityType::class, array(
                'class' => 'App:Voc',
                'placeholder' => 'Choose a GPS Distance Quality',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('voc')
                        ->where('voc.parent LIKE :parent')
                        ->setParameter('parent', 'precisionLatLong')
                        ->orderBy('voc.libelle', 'ASC');
                },
                'choice_translation_domain' => true,
                'choice_label' => 'libelle',
                'multiple' => false,
                'expanded' => false
            ))
            ->add('altitudeM')
            ->add('commentaireStation')
            ->addEventSubscriber($this->addUserDate);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Station'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_station';
    }
}
