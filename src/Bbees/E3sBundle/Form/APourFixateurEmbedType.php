<?php

namespace Bbees\E3sBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class APourFixateurEmbedType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
                $builder->add('fixateurVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                       'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('voc')
                                    ->where('voc.parent LIKE :parent')
                                    ->setParameter('parent', 'fixateur')
                                    ->orderBy('voc.libelle', 'ASC');
                            }, 
                        'choice_translation_domain' => true, 'choice_label' => 'libelle', 'multiple' => false, 'expanded' => false, 'label' => false, 'placeholder' => 'Choose a Fixateur',)
                            );
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bbees\E3sBundle\Entity\APourFixateur'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_apourfixateur_embed';
    }


}
