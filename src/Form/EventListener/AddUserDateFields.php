<?php

namespace App\Form\EventListener;

use App\Form\Enums\Action;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Security;

class AddUserDateFields implements EventSubscriberInterface {
  protected $security;

  public function __construct(Security $security) {
    $this->security = $security;
  }

  public static function getSubscribedEvents() {
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
    $data->setMetaCreationUser($data->getMetaCreationUser() || $data->getMetaCreationDate()
      ? $data->getMetaCreationUser()
      : $user->getId());
    $data->setMetaUpdateUser($user->getId());

    // This is why we are setting the date *after* setting the user
    $now = new DateTime();
    $data->setMetaCreationDate($data->getMetaCreationDate() ?: $now);
    $data->setMetaUpdateDate($now);

    $event->setData($data);
  }

  public function onPreSetData(FormEvent $event) {
    $form = $event->getForm();
    $form_type = $form->getConfig()->getOption("action_type");

    if ($form_type == Action::show()) {
      $form->add('metaCreationDate', DateTimeType::class, [
        'widget' => 'single_text',
        'html5' => false,
      ])
        ->add('metaUpdateDate', DateTimeType::class, [
          'widget' => 'single_text',
          'html5' => false,
        ]);
    }
  }
}
