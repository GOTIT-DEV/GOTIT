<?php

namespace Bbees\E3sBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EspeceIdentifieeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('dateIdentification')
                ->add('datePrecisionVocFk')
                ->add('commentaireEspId')
                ->add('critereIdentificationVocFk')
                ->add('referentielTaxonFk')
                ->add('dateCre')->add('dateMaj')->add('userCre')->add('userMaj')
                ->add('sequenceAssembleeExtFk')
                ->add('lotMaterielExtFk')
                ->add('lotMaterielFk')
                ->add('individuFk')
                ->add('sequenceAssembleeFk');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bbees\E3sBundle\Entity\EspeceIdentifiee'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_especeidentifiee';
    }


}
