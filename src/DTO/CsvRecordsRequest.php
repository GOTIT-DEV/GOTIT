<?php

namespace App\DTO;

use Symfony\Component\Serializer\Annotation\Groups;

class CsvRecordsRequest {
  /**
   * @Groups({"csv:import"})
   */
  private string $csv;

  public function setCsv($csv) {
    $this->csv = $csv;
  }

  public function getCsv() {
    return $this->csv;
  }
}
