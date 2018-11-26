<?php

/*
 * This file is part of the E3sBundle.
 *
 * Copyright (c) 2018 Philippe Grison <philippe.grison@mnhn.fr>
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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Translation\Translator;

class EstAligneEtTraiteEmbedType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    // ->select($qb->expr()->concat('chromatogramme.codeChromato','vocSpecificite.code').' AS compil')
    // 'choice_label' => 'codeChromatoSpecificite', 
    // 'choice_label' => 'codeChromato'  
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('chromatogrammeFk', EntityType::class, array('class' => 'BbeesE3sBundle:Chromatogramme', 
                   'query_builder' => function (EntityRepository $er) use ( $options ){
                        $qb = $er->createQueryBuilder('chromatogramme');
                        return  $qb->leftJoin('BbeesE3sBundle:Pcr', 'pcr', 'WITH', 'chromatogramme.pcrFk = pcr.id')
                                ->leftJoin('BbeesE3sBundle:Adn', 'adn', 'WITH', 'pcr.adnFk = adn.id')
                                ->leftJoin('BbeesE3sBundle:Individu', 'individu', 'WITH', 'adn.individuFk = individu.id')
                                ->leftJoin('BbeesE3sBundle:Voc', 'vocSpecificite', 'WITH', 'pcr.specificiteVocFk = vocSpecificite.id')
                                ->where('pcr.geneVocFk = :geneVocFk')
                                ->andwhere('individu.id = :individuFk')
                                ->setParameters(array('individuFk'=> $options['individuFk'], 'geneVocFk'=> $options['geneVocFk']))
                                ;
                        }, 
                    'choice_label' => 'codeChromatoSpecificite'               
                    ,'multiple' => false, 'expanded' => false, 'required' => true, 'label' => 'Code Chromato | Specificite', 'placeholder' => 'Choose a chromatogramme',)
                        )
                ->add('dateCre', DateTimeType::class, array( 'required' => false, 'widget' => 'single_text', 'format' => 'Y-MM-dd HH:mm:ss', 'html5' => false, 'data' =>  new \DateTime("now"), 'label' => false, 'attr'=>array('style'=>'display:none;')))
                ->add('dateMaj', DateTimeType::class, array( 'required' => false,  'widget' => 'single_text', 'format' => 'Y-MM-dd HH:mm:ss', 'html5' => false, 'data' =>  new \DateTime("now"), 'label' => false, 'attr'=>array('style'=>'display:none;')))
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
            'data_class' => 'Bbees\E3sBundle\Entity\EstAligneEtTraite',
            'geneVocFk' => 0,
            'individuFk' => 0
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_estaligneettraite';
    }


}
