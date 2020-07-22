<?php

namespace App\Form;

use App\Form\Action;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionFormType extends AbstractType
{
  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    parent::buildView($view, $form, $options);

    // Expose action_type in form templates
    $view->vars['action_type'] = $options['action_type'];
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setRequired([
      'action_type'
    ]);
    $resolver->setDefault('disabled', function (Options $options) {
      return $options['action_type'] == Action::show();
    });
  }
}
