<?php

namespace Bbees\E3sBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class EstIdentifieParEmbedType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('personneFk', EntityType::class, array('class' => 'BbeesE3sBundle:Personne', 
                      'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('personne')
                                    ->orderBy('personne.nomPersonne', 'ASC');
                            }, 
                       'choice_label' => 'nom_personne', 'multiple' => false, 'expanded' => false, 'label' => false,
                       'placeholder' => 'Choose a Person',))
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
            'data_class' => 'Bbees\E3sBundle\Entity\EstIdentifiePar'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_estidentifiepar';
    }


}
