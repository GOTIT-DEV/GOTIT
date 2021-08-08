<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExternalSequenceAssembler
 *
 * @ORM\Table(name="external_sequence_is_entered_by",
 *  indexes={
 *      @ORM\Index(name="IDX_DC41E25ACDD1F756", columns={"external_sequence_fk"}),
 *      @ORM\Index(name="IDX_DC41E25AB53CD04C", columns={"person_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class ExternalSequenceAssembler extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="external_sequence_is_entered_by_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var \SequenceAssembleeExt
   *
   * @ORM\ManyToOne(targetEntity="SequenceAssembleeExt", inversedBy="assemblers")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="external_sequence_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $sequenceAssembleeExtFk;

  /**
   * @var \Personne
   *
   * @ORM\ManyToOne(targetEntity="Personne")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="person_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $personneFk;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set sequenceAssembleeExtFk
   *
   * @param \App\Entity\SequenceAssembleeExt $sequenceAssembleeExtFk
   *
   * @return ExternalSequenceAssembler
   */
  public function setSequenceAssembleeExtFk(\App\Entity\SequenceAssembleeExt $sequenceAssembleeExtFk = null) {
    $this->sequenceAssembleeExtFk = $sequenceAssembleeExtFk;

    return $this;
  }

  /**
   * Get sequenceAssembleeExtFk
   *
   * @return \App\Entity\SequenceAssembleeExt
   */
  public function getSequenceAssembleeExtFk() {
    return $this->sequenceAssembleeExtFk;
  }

  /**
   * Set personneFk
   *
   * @param \App\Entity\Personne $personneFk
   *
   * @return ExternalSequenceAssembler
   */
  public function setPersonneFk(\App\Entity\Personne $personneFk = null) {
    $this->personneFk = $personneFk;

    return $this;
  }

  /**
   * Get personneFk
   *
   * @return \App\Entity\Personne
   */
  public function getPersonneFk() {
    return $this->personneFk;
  }
}
