<?php

namespace App\Form\EventListener;

use App\Form\Action;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AddUserDateFields implements EventSubscriberInterface
{
  private $tokenStorage;
  public function __construct(TokenStorageInterface $tokenStorage)
  {
    // Token storage allows to retrieve current user
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
    $form = $event->getForm();
    $form_type = $form->getConfig()->getOption("action_type");

    if ($form_type == Action::show()) {
      $form->add('dateCre', DateTimeType::class, [
        'widget' => 'single_text'
      ])
        ->add('dateMaj', DateTimeType::class, [
          'widget' => 'single_text'
        ]);
    } else {
      $data = $event->getData();
      
      $now = new DateTime();
      $data->setDateCre($data->getDateCre() ?: $now);
      $data->setDateMaj($now);
      
      $user = $this->tokenStorage->getToken()->getUser();
      $data->setUserCre($data->getUserCre() ?: $user->getId());
      $data->setUserMaj($user->getId());

      $event->setData($data);
    }
  }
}
