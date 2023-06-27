<?php

namespace App\Form;

use App\Form\DataTransformer\UppercaseTransformer;
use App\Form\Enums\Action;
use App\Form\EventListener\AddUserDateFields;
use App\Form\UserDateTraceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class ActionFormType extends UserDateTraceType {

  protected $security;
  protected $er;
  protected $translator;
  protected $uppercase_transformer;
  /**
   * {@inheritdoc}
   */
  public function __construct(
    AddUserDateFields $addUserDate,
    Security $security,
    EntityManagerInterface $em,
    TranslatorInterface $translator
  ) {
    parent::__construct($addUserDate);
    $this->security = $security;
    $this->er = $em;
    $this->translator = $translator;
    $this->uppercase_transformer = new UppercaseTransformer();
  }

  protected function upperCaseFields(FormBuilderInterface $builder, array $fields) {
    foreach ($fields as $fieldName) {
      $field = $builder->get($fieldName);
      $fieldClass = get_class($field->getType()->getInnerType());
      $options = $field->getOptions();

      $options['attr']['class'] = $options['attr']['class'] ?? "";
      $options['attr']['class'] .= " text-uppercase";
      $builder->add($fieldName, $fieldClass, $options);
      $builder->get($fieldName)
        ->addModelTransformer($this->uppercase_transformer);
    }
  }

  public function buildView(FormView $view, FormInterface $form, array $options) {
    parent::buildView($view, $form, $options);

    // Expose action_type in form templates
    $view->vars['action_type'] = $options['action_type'];
  }

  public function canEditAdminOnly(array $options) {
    return $options['action_type'] == "edit" && !$this->security->isGranted('ROLE_ADMIN');
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    parent::configureOptions($resolver);
    $resolver->setRequired([
      'action_type',
    ]);
    $resolver->setDefault('disabled', function (Options $options) {
      return $options['action_type'] == Action::show->value;
    });
  }
}
