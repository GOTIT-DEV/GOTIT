<?php

namespace App\Entity;

use App\Entity\AbstractTimestampedEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * DnaExtraction
 *
 * @ORM\Table(name="dna_is_extracted_by",
 *  indexes={
 *      @ORM\Index(name="IDX_B786C5214B06319D", columns={"dna_fk"}),
 *      @ORM\Index(name="IDX_B786C521B53CD04C", columns={"person_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class DnaExtraction extends AbstractTimestampedEntity {
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
   * @ORM\ManyToOne(targetEntity="Dna", inversedBy="dnaExtractions")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="dna_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $adnFk;

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
   * Set adnFk
   *
   * @param \App\Entity\Dna $adnFk
   *
   * @return DnaExtraction
   */
  public function setAdnFk(\App\Entity\Dna $adnFk = null) {
    $this->adnFk = $adnFk;

    return $this;
  }

  /**
   * Get adnFk
   *
   * @return \App\Entity\Dna
   */
  public function getAdnFk() {
    return $this->adnFk;
  }

  /**
   * Set personneFk
   *
   * @param \App\Entity\Personne $personneFk
   *
   * @return DnaExtraction
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
