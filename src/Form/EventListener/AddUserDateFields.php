<?php

namespace App\Form\EventListener;

use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AddUserDateFields implements EventSubscriberInterface
{
  private $tokenStorage;
  public function __construct(TokenStorageInterface $tokenStorage)
  {
    $this->tokenStorage = $tokenStorage;
  }

  public static function getSubscribedEvents()
  {
    return [
      FormEvents::PRE_SET_DATA  => 'onPreSetData',
    ];
  }


  public function onPreSetData(FormEvent $event)
  {
    $user = $this->tokenStorage->getToken()->getUser();
    $now = new DateTime();
    $data = $event->getData();
    $form = $event->getForm();

    $data->setDateCre($data->getDateCre() ?: $now);
    $data->setDateMaj($now);

    $data->setUserCre($data->getUserCre() ?: $user->getId());
    $data->setUserMaj($user->getId());

    $event->setData($data);
  }
}
