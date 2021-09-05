<?php

namespace App\Entity;

use App\Entity\AbstractTimestampedEntity;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * DnaProducer
 *
 * @ORM\Table(name="dna_is_extracted_by",
 *  indexes={
 *      @ORM\Index(name="IDX_B786C5214B06319D", columns={"dna_fk"}),
 *      @ORM\Index(name="IDX_B786C521B53CD04C", columns={"person_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class DnaProducer extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="dna_is_extracted_by_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var \Dna
   *
   * @ORM\ManyToOne(targetEntity="Dna", inversedBy="dnaProducers", fetch="EAGER")
   * @ORM\JoinColumn(name="dna_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * @Groups({"field"})
   */
  private $dnaFk;

  /**
   * @var \Person
   *
   * @ORM\ManyToOne(targetEntity="Person", fetch="EAGER")
   * @ORM\JoinColumn(name="person_fk", referencedColumnName="id", nullable=false)
   * @Groups({"field"})
   */
  private $personFk;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set dnaFk
   *
   * @param \App\Entity\Dna $dnaFk
   *
   * @return DnaProducer
   */
  public function setDnaFk(\App\Entity\Dna $dnaFk = null) {
    $this->dnaFk = $dnaFk;

    return $this;
  }

  /**
   * Get dnaFk
   *
   * @return \App\Entity\Dna
   */
  public function getDnaFk() {
    return $this->dnaFk;
  }

  /**
   * Set personFk
   *
   * @param \App\Entity\Person $personFk
   *
   * @return DnaProducer
   */
  public function setPersonFk(\App\Entity\Person $personFk = null) {
    $this->personFk = $personFk;

    return $this;
  }

  /**
   * Get personFk
   *
   * @return \App\Entity\Person
   */
  public function getPersonFk() {
    return $this->personFk;
  }
}