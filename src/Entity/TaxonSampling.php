<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\AbstractTimestampedEntity;

/**
 * TaxonSampling
 *
 * @ORM\Table(name="has_targeted_taxa",
 *  indexes={
 *      @ORM\Index(name="IDX_C0DF0CE4662D9B98", columns={"sampling_fk"}),
 *      @ORM\Index(name="IDX_C0DF0CE47B09E3BC", columns={"taxon_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class TaxonSampling extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="has_targeted_taxa_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var Collecte
   *
   * @ORM\ManyToOne(targetEntity="Collecte", inversedBy="taxonSamplings")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="sampling_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $collecteFk;

  /**
   * @var ReferentielTaxon
   *
   * @ORM\ManyToOne(targetEntity="ReferentielTaxon")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="taxon_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $referentielTaxonFk;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set collecteFk
   *
   * @param Collecte $collecteFk
   * @return TaxonSampling
   */
  public function setCollecteFk(Collecte $collecteFk = null) {
    $this->collecteFk = $collecteFk;
    return $this;
  }

  /**
   * Get collecteFk
   *
   * @return Collecte
   */
  public function getCollecteFk() {
    return $this->collecteFk;
  }

  /**
   * Set referentielTaxonFk
   *
   * @param ReferentielTaxon $referentielTaxonFk
   * @return TaxonSampling
   */
  public function setReferentielTaxonFk(ReferentielTaxon $referentielTaxonFk = null) {
    $this->referentielTaxonFk = $referentielTaxonFk;
    return $this;
  }

  /**
   * Get referentielTaxonFk

   * @return ReferentielTaxon
   */
  public function getReferentielTaxonFk() {
    return $this->referentielTaxonFk;
  }
}
