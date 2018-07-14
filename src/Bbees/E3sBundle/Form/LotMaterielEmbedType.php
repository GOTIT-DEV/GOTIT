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

class LotMaterielEmbedType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder->add('codeLotMateriel')
                ->add('dateCre', DateTimeType::class, array( 'required' => false, 'widget' => 'single_text', 'format' => 'Y-MM-dd HH:mm:ss', 'html5' => false, 'label' => false, 'attr'=>array('style'=>'display:none;')))
                ->add('dateMaj', DateTimeType::class, array( 'required' => false, 'widget' => 'single_text', 'format' => 'Y-MM-dd HH:mm:ss', 'html5' => false, 'data' =>  new \DateTime("now"), 'label' => false, 'attr'=>array('style'=>'display:none;')))
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
            'data_class' => 'Bbees\E3sBundle\Entity\LotMateriel'
        ));       
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_lotmateriel';
    }


}
