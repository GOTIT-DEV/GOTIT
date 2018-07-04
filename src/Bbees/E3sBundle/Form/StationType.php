<?php

namespace Bbees\E3sBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class StationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('codeStation')->add('nomStation')
                ->add('infoDescription') 
                ->add('paysFk', EntityType::class, array('class' => 'BbeesE3sBundle:Pays','placeholder' => 'Choose a Country', 'choice_label' => 'nom_pays', 'multiple' => false, 'expanded' => false))
                ->add('communeFk', EntityType::class, array('class' => 'BbeesE3sBundle:Commune',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('commune')
                                    ->orderBy('commune.codeCommune', 'ASC');
                        },
                    'choice_label' => 'code_commune', 'multiple' => false, 'expanded' => false,'placeholder' => 'Choose a Commune')) 
                ->add('habitatTypeVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 'placeholder' => 'Choose an Habitat Type',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                                    ->where('voc.parent LIKE :parent')
                                    ->setParameter('parent', 'habitatType')
                                    ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_label' => 'libelle', 'multiple' => false, 'expanded' => false))
                ->add('pointAccesVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 'placeholder' => 'Choose an Access Point',
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                                    ->where('voc.parent LIKE :parent')
                                    ->setParameter('parent', 'pointAcces')
                                    ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_label' => 'libelle', 'multiple' => false, 'expanded' => false))
                ->add('latDegDec')->add('longDegDec')
                ->add('precisionLatLongVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 'placeholder' => 'Choose a GPS Distance Quality',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                                    ->where('voc.parent LIKE :parent')
                                    ->setParameter('parent', 'precisionLatLong')
                                    ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_label' => 'libelle', 'multiple' => false, 'expanded' => false))
                ->add('altitudeM')
                ->add('commentaireStation')
                ->add('dateCre', DateTimeType::class, array( 'required' => false, 'widget' => 'single_text', 'format' => 'Y-MM-dd HH:mm:ss', 'html5' => false, 'disabled' => true ))
                ->add('dateMaj', DateTimeType::class, array( 'required' => false,  'widget' => 'single_text', 'format' => 'Y-MM-dd HH:mm:ss', 'html5' => false, ))
                ->add('userCre', HiddenType::class, array())
                ->add('userMaj', HiddenType::class, array());
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bbees\E3sBundle\Entity\Station'
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
