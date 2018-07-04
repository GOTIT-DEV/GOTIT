<?php

namespace Bbees\E3sBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class ACiblerEmbedType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('referentielTaxonFk', EntityType::class, array('class' => 'BbeesE3sBundle:ReferentielTaxon', 
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('rt')
                                    ->orderBy('rt.taxname', 'ASC');
                            }, 
                        'choice_label' => 'taxname', 'multiple' => false, 'expanded' => false, 'label' => false, 
                        'placeholder' => 'Choose a Taxon',)
               );
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bbees\E3sBundle\Entity\ACibler'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_acibler';
    }


}
