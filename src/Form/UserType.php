<?php

namespace App\Form;

use App\Form\EventListener\AddUserDateFields;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

class UserType extends ActionFormType
{
    private $addUserDate;

    /**
     * {@inheritdoc}
     */
    public function __construct(TokenStorageInterface $tokenStorage, Security $security)
    {
      $this->addUserDate = new AddUserDateFields($tokenStorage);
      $this->security = $security;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $action_type = $options['action_type'];
        $editAdminOnly = ($action_type == "edit" && !$this->security->isGranted('ROLE_ADMIN'));

        $isAdminForm = $builder->getData()->getRole() == "ROLE_ADMIN";

        $builder->add('username')
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))
            ->add('email', EmailType::class, array(
                'required' => false,
                ))
            ->add('name')
            ->add('institution')
            ->add('role', ChoiceType::class, array(
                'disabled' => $editAdminOnly || $isAdminForm,
                'choices'  => array(
                    'ADMIN' => 'ROLE_ADMIN', 
                    'PROJECT' => 'ROLE_PROJECT', 
                    'COLLABORATION' => 'ROLE_COLLABORATION', 
                    'INVITED' => 'ROLE_INVITED', 
                    'LOCKED' => 'ROLE_LOCKED',
                ), 
                'required' => true,
                'choice_translation_domain' => false, 
                'multiple' => false, 
                'expanded' => true, 
                'label_attr' => array('class' => 'radio-inline'),
            ))
            ->add('commentaireUser')
            ->addEventSubscriber($this->addUserDate);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bbees_userbundle_user';
    }
}
