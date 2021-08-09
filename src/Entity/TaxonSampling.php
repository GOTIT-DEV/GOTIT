<?php

namespace App\Entity;

use App\Entity\AbstractTimestampedEntity;
use Doctrine\ORM\Mapping as ORM;

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
   * @var Sampling
   *
   * @ORM\ManyToOne(targetEntity="Sampling", inversedBy="taxonSamplings")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="sampling_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $samplingFk;

  /**
   * @var Taxon
   *
   * @ORM\ManyToOne(targetEntity="Taxon")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="taxon_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $taxonFk;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set samplingFk
   *
   * @param Sampling $samplingFk
   * @return TaxonSampling
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

  /**
   * Set taxonFk
   *
   * @param Taxon $taxonFk
   * @return TaxonSampling
   */
  public function setTaxonFk(Taxon $taxonFk = null) {
    $this->taxonFk = $taxonFk;
    return $this;
  }

  /**
   * Get taxonFk

   * @return Taxon
   */
  public function getTaxonFk() {
    return $this->taxonFk;
  }
}
