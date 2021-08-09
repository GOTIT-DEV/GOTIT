<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * InternalSequenceAssembly
 *
 * @ORM\Table(name="chromatogram_is_processed_to",
 *  indexes={
 *      @ORM\Index(name="IDX_BD45639EEFCFD332", columns={"chromatogram_fk"}),
 *      @ORM\Index(name="IDX_BD45639E5BE90E48", columns={"internal_sequence_fk"})})
 * @ORM\Entity
 * @UniqueEntity(
 *  fields={"chromatogrammeFk", "internalSequenceFk"},
 *  message = "Duplicated sequence to chromatogram relation"
 * )
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class InternalSequenceAssembly extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="chromatogram_is_processed_to_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var \Chromatogramme
   *
   * @ORM\ManyToOne(targetEntity="Chromatogramme")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="chromatogram_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $chromatogrammeFk;

  /**
   * @var \InternalSequence
   *
   * @ORM\ManyToOne(targetEntity="InternalSequence", inversedBy="assemblies")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="internal_sequence_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $internalSequenceFk;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set chromatogrammeFk
   *
   * @param \App\Entity\Chromatogramme $chromatogrammeFk
   *
   * @return InternalSequenceAssembly
   */
  public function setChromatogrammeFk(\App\Entity\Chromatogramme $chromatogrammeFk = null) {
    $this->chromatogrammeFk = $chromatogrammeFk;

    return $this;
  }

  /**
   * Get chromatogrammeFk
   *
   * @return \App\Entity\Chromatogramme
   */
  public function getChromatogrammeFk() {
    return $this->chromatogrammeFk;
  }

  /**
   * Set internalSequenceFk
   *
   * @param \App\Entity\InternalSequence $internalSequenceFk
   *
   * @return InternalSequenceAssembly
   */
  public function setInternalSequenceFk(\App\Entity\InternalSequence $internalSequenceFk = null) {
    $this->internalSequenceFk = $internalSequenceFk;

    return $this;
  }

  /**
   * Get internalSequenceFk
   *
   * @return \App\Entity\InternalSequence
   */
  public function getInternalSequenceFk() {
    return $this->internalSequenceFk;
  }
}
