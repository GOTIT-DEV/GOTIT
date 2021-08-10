<?php

/*
 * This file is part of the E3sBundle.
 *
 * Authors : see information concerning authors of GOTIT project in file AUTHORS.md
 *
 * E3sBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * E3sBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with E3sBundle.  If not, see <https://www.gnu.org/licenses/>
 *
 */

namespace App\Entity;

use App\Entity\AbstractTimestampedEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * SamplingMethod
 *
 * @ORM\Table(name="sampling_is_done_with_method",
 *  indexes={
 *      @ORM\Index(name="IDX_5A6BD88A29B38195", columns={"sampling_method_voc_fk"}),
 *      @ORM\Index(name="IDX_5A6BD88A662D9B98", columns={"sampling_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class SamplingMethod extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="sampling_is_done_with_method_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="sampling_method_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $samplingMethodVocFk;

  /**
   * @var Sampling
   *
   * @ORM\ManyToOne(targetEntity="Sampling", inversedBy="samplingMethods")
   * @ORM\JoinColumn(name="sampling_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   */
  private $samplingFk;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set samplingMethodVocFk
   *
   * @param Voc $samplingMethodVocFk
   * @return SamplingMethod
   */
  public function setSamplingMethodVocFk(Voc $samplingMethodVocFk = null) {
    $this->samplingMethodVocFk = $samplingMethodVocFk;

    return $this;
  }

  /**
   * Get samplingMethodVocFk
   *
   * @return Voc
   */
  public function getSamplingMethodVocFk() {
    return $this->samplingMethodVocFk;
  }

  /**
   * Set samplingFk
   *
   * @param Sampling $samplingFk
   * @return SamplingMethod
   */
  public function setSamplingFk(Sampling $samplingFk = null) {
    $this->samplingFk = $samplingFk;

    return $this;
  }

  /**
   * Get samplingFk
   *
   * @return Sampling
   */
  public function getSamplingFk() {
    return $this->samplingFk;
  }
}
