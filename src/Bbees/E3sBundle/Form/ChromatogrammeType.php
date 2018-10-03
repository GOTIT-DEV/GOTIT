<?php

namespace Bbees\E3sBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ChromatogrammeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pcrFk',EntityType::class, array('class' => 'BbeesE3sBundle:Pcr',
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('pcr')
                                    ->orderBy('pcr.codePcr', 'ASC');
                        },
                        'placeholder' => 'Choose a PCR', 'choice_label' => 'code_pcr', 'multiple' => false, 'expanded' => false))    
                ->add('codeChromato')
                ->add('numYas',  TextType::class, array( 'required' => true,))
                ->add('primerChromatoVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                                    ->where('voc.parent LIKE :parent')
                                    ->setParameter('parent', 'primerChromato')
                                    ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_translation_domain' => true, 'choice_label' => 'libelle', 'multiple' => false, 'expanded' => false,'placeholder' => 'Choose a primer')) 
                ->add('qualiteChromatoVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                                    ->where('voc.parent LIKE :parent')
                                    ->setParameter('parent', 'qualiteChromato')
                                    ->orderBy('voc.libelle', 'ASC');
                        }, 
                    'choice_translation_domain' => true, 'choice_label' => 'libelle', 'multiple' => false, 'expanded' => false,'placeholder' => 'Choose a quality')) 
                ->add('etablissementFk',EntityType::class, array('class' => 'BbeesE3sBundle:Etablissement',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('etablissement')
                                    ->orderBy('etablissement.nomEtablissement', 'ASC');
                        },
                    'placeholder' => 'Choose a socitety', 'choice_label' => 'nom_etablissement', 'multiple' => false, 'expanded' => false)) 
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
            'data_class' => 'Bbees\E3sBundle\Entity\Chromatogramme'
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
