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
   * @var \Person
   *
   * @ORM\ManyToOne(targetEntity="Person")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="person_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $personFk;

  /**
   * @var \TaxonIdentification
   *
   * @ORM\ManyToOne(targetEntity="TaxonIdentification", inversedBy="personSpeciesIds")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="identified_species_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $taxonIdentificationFk;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set personFk
   *
   * @param \App\Entity\Person $personFk
   *
   * @return PersonSpeciesId
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

  /**
   * Set taxonIdentificationFk
   *
   * @param \App\Entity\TaxonIdentification $taxonIdentificationFk
   *
   * @return PersonSpeciesId
   */
  public function setTaxonIdentificationFk(\App\Entity\TaxonIdentification $taxonIdentificationFk = null) {
    $this->taxonIdentificationFk = $taxonIdentificationFk;

    return $this;
  }

  /**
   * Get taxonIdentificationFk
   *
   * @return \App\Entity\TaxonIdentification
   */
  public function getTaxonIdentificationFk() {
    return $this->taxonIdentificationFk;
  }
}
