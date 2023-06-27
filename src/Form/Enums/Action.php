<?php


namespace App\Form\Enums;

enum Action: string {
  case show = 'show';
  case create = 'new';
  case edit = 'edit';
  case delete = 'delete';
}
