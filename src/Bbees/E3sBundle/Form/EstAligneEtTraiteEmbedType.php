<?php

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
