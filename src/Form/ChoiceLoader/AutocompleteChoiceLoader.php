<?php

// https://stackoverflow.com/questions/35456199/symfony-2-8-dynamic-choicetype-options

namespace App\Form\ChoiceLoader;

use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Doctrine\ORM\EntityRepository;

class AutocompleteChoiceLoader implements ChoiceLoaderInterface {
  /** @var ChoiceListInterface */
  private $choiceList;
  protected $repository;
  protected $choice_value;

  public function __construct(EntityRepository $er, String $choice_value) {
    $this->repository   = $er;
    $this->choice_value = $choice_value;
  }

  public function loadValuesForChoices(array $choices, $value = null):array {
    $choices = array_filter($choices, function ($c) {return $c != null;});

    $values = array();
    foreach ($choices as $key => $choice) {
      if (is_callable($value)) {
        $values[$key] = (string) call_user_func($value, $choice, $key);
      } else {
        $values[$key] = $choice;
      }
    }

    $this->choiceList = new ArrayChoiceList($choices, $value);
    return $values;
  }

  public function loadChoiceList($value = null):ChoiceListInterface {
    // is called on form view create after loadValuesForChoices of form create
    if ($this->choiceList instanceof ChoiceListInterface) {
      return $this->choiceList;
    }

    // if no values preset yet return empty list
    $this->choiceList = new ArrayChoiceList([], $value);

    return $this->choiceList;
  }

  public function loadChoicesForValues(array $values, $value = null):array {

    $choices          = $this->repository->findBy([$this->choice_value => $values]);
    $this->choiceList = new ArrayChoiceList($choices, $value);
    return $choices;
  }
}