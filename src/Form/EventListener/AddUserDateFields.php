<?php

namespace App\Form\EventListener;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use DateTime;
use App\Form\Enums\Action;

class AddUserDateFields implements EventSubscriberInterface {
  protected $security;

  public function __construct(Security $security) {
    $this->security = $security;
  }

  public static function getSubscribedEvents():array {
    return [
      FormEvents::PRE_SET_DATA => 'onPreSetData',
      FormEvents::SUBMIT => 'onSubmit',
    ];
  }

  public function onSubmit(FormEvent $event) {
    $data = $event->getData();

    $user = $this->security->getUser();

    /* Do not replace creator user if creation date is defined :
    it means that the record was already existing,
    but creator user was not known*/
    $data->setUserCre($data->getUserCre() || $data->getDateCre()
      ? $data->getUserCre()
      : $user->getId());
    $data->setUserMaj($user->getId());

    // This is why we are setting the date *after* setting the user
    $now = new DateTime();
    $data->setDateCre($data->getDateCre() ?: $now);
    $data->setDateMaj($now);

    $event->setData($data);
  }

  public function onPreSetData(FormEvent $event) {
    $form = $event->getForm();
    $form_type = $form->getConfig()->getOption("action_type");

    if ($form_type == Action::show->value) {
      $form->add('dateCre', DateTimeType::class, [
        'widget' => 'single_text',
        'html5' => false,
      ])
        ->add('dateMaj', DateTimeType::class, [
          'widget' => 'single_text',
          'html5' => false,
        ]);
    }
  }
}
