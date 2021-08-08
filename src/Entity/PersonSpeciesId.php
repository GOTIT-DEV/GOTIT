<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PersonSpeciesId
 *
 * @ORM\Table(name="species_is_identified_by",
 *  indexes={
 *      @ORM\Index(name="IDX_F8FCCF63B53CD04C", columns={"person_fk"}),
 *      @ORM\Index(name="IDX_F8FCCF63B4AB6BA0", columns={"identified_species_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class PersonSpeciesId extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="species_is_identified_by_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

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
   * @var \EspeceIdentifiee
   *
   * @ORM\ManyToOne(targetEntity="EspeceIdentifiee", inversedBy="personSpeciesIds")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="identified_species_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $especeIdentifieeFk;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set personneFk
   *
   * @param \App\Entity\Personne $personneFk
   *
   * @return PersonSpeciesId
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

  /**
   * Set especeIdentifieeFk
   *
   * @param \App\Entity\EspeceIdentifiee $especeIdentifieeFk
   *
   * @return PersonSpeciesId
   */
  public function setEspeceIdentifieeFk(\App\Entity\EspeceIdentifiee $especeIdentifieeFk = null) {
    $this->especeIdentifieeFk = $especeIdentifieeFk;

    return $this;
  }

  /**
   * Get especeIdentifieeFk
   *
   * @return \App\Entity\EspeceIdentifiee
   */
  public function getEspeceIdentifieeFk() {
    return $this->especeIdentifieeFk;
  }
}
