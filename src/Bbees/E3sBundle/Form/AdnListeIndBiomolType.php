<?php

namespace Bbees\E3sBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AdnListeIndBiomolType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('individuFk',EntityType::class, array('class' => 'BbeesE3sBundle:Individu',
                         'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('ind')
                               ->where('ind.codeIndBiomol IS NOT NULL')
                               ->orderBy('ind.codeIndBiomol', 'ASC');
                        }, 
                         'placeholder' => 'Choose an individu', 'choice_label' => 'code_ind_biomol', 'multiple' => false, 'expanded' => false))
                ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bbees\E3sBundle\Entity\Adn'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_adn';
    }


}
