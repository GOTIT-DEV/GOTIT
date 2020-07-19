<?php

/*
 * This file is part of the E3sBundle.
 *
 * Authors : see information concerning authors of GOTIT project in file AUTHORS.md
 *
 * E3sBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * E3sBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with E3sBundle.  If not, see <https://www.gnu.org/licenses/>
 * 
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\DataTransformer\UppercaseTransformer;
use App\Form\EventListener\AddUserDateFields;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PersonneType extends AbstractType
{
    private $uppercaseTrans;
    private $addUserDate;
    
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->uppercaseTrans = new UppercaseTransformer();
        $this->addUserDate = new AddUserDateFields($tokenStorage);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nomPersonne', TextType::class, [
            'attr' => ["class" => "text-uppercase"]
        ])
            ->add('nomComplet', TextType::class, [
                'attr' => ["class" => "text-uppercase"],
                'required' => false
            ])
            ->add('nomPersonneRef',  TextType::class, [
                'attr' => ["class" => "text-uppercase"],
                'required' => false
            ])
            ->add('etablissementFk', EntityType::class, array(
                'class' => 'App:Etablissement',
                'placeholder' => 'Choose a Etablissement',
                'choice_label' => 'nom_etablissement',
                'multiple' => false,
                'expanded' => false,
                'required' => false,
            ))
            ->add('commentairePersonne');

        $builder->addEventSubscriber($this->addUserDate);

        // force uppercase on these fields
        $builder->get('nomPersonne')->addModelTransformer($this->uppercaseTrans);
        $builder->get('nomComplet')->addModelTransformer($this->uppercaseTrans);
        $builder->get('nomPersonneRef')->addModelTransformer($this->uppercaseTrans);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Personne'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_e3sbundle_personne';
    }
}
