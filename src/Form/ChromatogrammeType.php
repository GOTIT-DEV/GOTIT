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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ChromatogrammeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pcrTypeahead', null, ['mapped' => false, 'attr' => ['class' => 'typeahead typeahead-pcr', 'data-target_id' => "bbees_e3sbundle_chromatogramme_pcrId", 'name' => "where", 'placeholder' => "Pcr typeahead placeholder",  "maxlength" => "255"], 'required' => true, ])
                ->add('pcrId', HiddenType::class, array( 'mapped' => false, 'required' => true, ))                
                ->add('codeChromato')
                ->add('numYas',  TextType::class, array( 'required' => true,))
                ->add('primerChromatoVocFk', EntityType::class, array('class' => 'App:Voc', 
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                                    ->where('voc.parent LIKE :parent')
                                    ->setParameter('parent', 'primerChromato')
                                    ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_translation_domain' => true, 'choice_label' => 'libelle', 'multiple' => false, 'expanded' => false,'placeholder' => 'Choose a primer')) 
                ->add('qualiteChromatoVocFk', EntityType::class, array('class' => 'App:Voc', 
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                                    ->where('voc.parent LIKE :parent')
                                    ->setParameter('parent', 'qualiteChromato')
                                    ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_translation_domain' => true, 'choice_label' => 'libelle', 'multiple' => false, 'expanded' => false,'placeholder' => 'Choose a quality')) 
                ->add('etablissementFk',EntityType::class, array('class' => 'App:Etablissement',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('etablissement')
                                    ->orderBy('etablissement.nomEtablissement', 'ASC');
                        },
                    'placeholder' => 'Choose a society', 'choice_label' => 'nom_etablissement', 'multiple' => false, 'expanded' => false)) 
                ->add('commentaireChromato')
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
            'data_class' => 'App\Entity\Chromatogramme'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_chromatogramme';
    }


}
