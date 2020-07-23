<?php

namespace App\Form\EventListener;

use App\Form\Enums\Action;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Security;

class AddUserDateFields implements EventSubscriberInterface
{
  protected $security;

  public function __construct(Security $security)
  {
    $this->security = $security;
  }

  public static function getSubscribedEvents()
  {
    return [
      FormEvents::PRE_SET_DATA  => 'onPreSetData',
      FormEvents::SUBMIT  => 'onSubmit',
    ];
  }

  public function onSubmit(FormEvent $event)
  {
    $data = $event->getData();

    $now = new DateTime();
    $data->setDateCre($data->getDateCre() ?: $now);
    $data->setDateMaj($now);

    $user = $this->security->getUser();
    $data->setUserCre($data->getUserCre() ?: $user->getId());
    $data->setUserMaj($user->getId());

    $event->setData($data);
  }

  public function onPreSetData(FormEvent $event)
  {
    $form = $event->getForm();
    $form_type = $form->getConfig()->getOption("action_type");

    if ($form_type == Action::show()) {
      $form->add('dateCre', DateTimeType::class, [
        'widget' => 'single_text'
      ])
        ->add('dateMaj', DateTimeType::class, [
          'widget' => 'single_text'
        ]);
    }
  }
}
