<?php

namespace App\Form;

use App\Form\Enums\Action;
use App\Form\EventListener\AddUserDateFields;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\UserDateTraceType;
use Symfony\Component\Security\Core\Security;

class ActionFormType extends UserDateTraceType
{
  /**
   * {@inheritdoc}
   */
  public function __construct(AddUserDateFields $addUserDate, Security $security)
  {
      parent::__construct($addUserDate);
      $this->security = $security;
  }

  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    parent::buildView($view, $form, $options);

    // Expose action_type in form templates
    $view->vars['action_type'] = $options['action_type'];
  }

  public function canEditAdminOnly(array $options)
  {
    return $options['action_type'] == "edit" && !$this->security->isGranted('ROLE_ADMIN');
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    parent::configureOptions($resolver);
    $resolver->setRequired([
      'action_type'
    ]);
    $resolver->setDefault('disabled', function (Options $options) {
      return $options['action_type'] == Action::show();
    });
  }
}
