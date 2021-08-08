<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InternalSequenceAssembler
 *
 * @ORM\Table(name="internal_sequence_is_assembled_by",
 *  indexes={
 *      @ORM\Index(name="IDX_F6971BA85BE90E48", columns={"internal_sequence_fk"}),
 *      @ORM\Index(name="IDX_F6971BA8B53CD04C", columns={"person_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class InternalSequenceAssembler extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="internal_sequence_is_assembled_by_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var \SequenceAssemblee
   *
   * @ORM\ManyToOne(targetEntity="SequenceAssemblee", inversedBy="assemblers")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="internal_sequence_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $sequenceAssembleeFk;

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
   * Set sequenceAssembleeFk
   *
   * @param \App\Entity\SequenceAssemblee $sequenceAssembleeFk
   *
   * @return InternalSequenceAssembler
   */
  public function setSequenceAssembleeFk(\App\Entity\SequenceAssemblee $sequenceAssembleeFk = null) {
    $this->sequenceAssembleeFk = $sequenceAssembleeFk;

    return $this;
  }

  /**
   * Get sequenceAssembleeFk
   *
   * @return \App\Entity\SequenceAssemblee
   */
  public function getSequenceAssembleeFk() {
    return $this->sequenceAssembleeFk;
  }

  /**
   * Set personneFk
   *
   * @param \App\Entity\Personne $personneFk
   *
   * @return InternalSequenceAssembler
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
