<?php

namespace App\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class ModalButtonType extends ButtonType {

  public function buildView(FormView $view, FormInterface $form, array $options) {
    parent::buildView($view, $form, $options);

    // Expose action_type in form templates
    $view->vars['icon_class'] = $options['icon_class'];
  }

  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setNormalizer('attr', function (Options $options, $value) {
      return array_merge($value, [
        'data-toggle' => "modal",
        'class' => $value['class'] . ' btn',
      ]);
    });
    $resolver->setDefined(["render_controller", 'icon_class']);
    $resolver->setDefault('icon_class', '');
  }

  public function getParent():?string {
    return ButtonType::class;
  }
}
