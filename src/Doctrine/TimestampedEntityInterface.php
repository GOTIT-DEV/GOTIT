<?php

namespace App\Doctrine;

interface TimestampedEntityInterface {
  public function setDateCre($dateCre);
  public function getDateCre();
  public function setDateMaj($dateMaj);
  public function getDateMaj();
  public function setUserCre($userCre);
  public function getUserCre();
  public function setUserMaj($userMaj);
  public function getUserMaj();
}