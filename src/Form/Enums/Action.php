<?php


namespace App\Form\Enums;

enum Action: string
{
   case show = 'show';
   case create = 'new';
   case edit = 'edit';
   case delete = 'delete';
}

/*
 use MyCLabs\Enum\Enum;


class Action extends Enum
{
  private const show = "show";
  private const create = "new";
  private const edit = "edit";
  private const delete = "delete";
}

 */
