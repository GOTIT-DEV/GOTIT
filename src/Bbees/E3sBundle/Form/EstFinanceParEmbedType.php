<?php

namespace Bbees\E3sBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class EstFinanceParEmbedType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('programmeFk', EntityType::class, array('class' => 'BbeesE3sBundle:Programme', 
                      'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('programme')
                                    ->orderBy('programme.codeProgramme', 'ASC');
                            }, 
                       'choice_label' => 'code_programme', 'multiple' => false, 'expanded' => false, 'label' => false, 
                       'placeholder' => 'Choose a Program',)
            );
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bbees\E3sBundle\Entity\EstFinancePar'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_estfinancepar';
    }


}
