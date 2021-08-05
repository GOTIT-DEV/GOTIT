<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompositionLotMateriel
 *
 * @ORM\Table(name="composition_of_internal_biological_material",
 *  indexes={
 *      @ORM\Index(name="IDX_10A697444236D33E", columns={"specimen_type_voc_fk"}),
 *      @ORM\Index(name="IDX_10A6974454DBBD4D", columns={"internal_biological_material_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class CompositionLotMateriel extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="composition_of_internal_biological_material_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var integer
   *
   * @ORM\Column(name="number_of_specimens", type="bigint", nullable=true)
   */
  private $nbIndividus;

  /**
   * @var string
   *
   * @ORM\Column(name="internal_biological_material_composition_comments", type="text", nullable=true)
   */
  private $commentaireCompoLotMateriel;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="specimen_type_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $typeIndividuVocFk;

  /**
   * @var \LotMateriel
   *
   * @ORM\ManyToOne(targetEntity="LotMateriel", inversedBy="compositionLotMateriels")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="internal_biological_material_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $lotMaterielFk;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set nbIndividus
   *
   * @param integer $nbIndividus
   *
   * @return CompositionLotMateriel
   */
  public function setNbIndividus($nbIndividus) {
    $this->nbIndividus = $nbIndividus;

    return $this;
  }

  /**
   * Get nbIndividus
   *
   * @return integer
   */
  public function getNbIndividus() {
    return $this->nbIndividus;
  }

  /**
   * Set commentaireCompoLotMateriel
   *
   * @param string $commentaireCompoLotMateriel
   *
   * @return CompositionLotMateriel
   */
  public function setCommentaireCompoLotMateriel($commentaireCompoLotMateriel) {
    $this->commentaireCompoLotMateriel = $commentaireCompoLotMateriel;

    return $this;
  }

  /**
   * Get commentaireCompoLotMateriel
   *
   * @return string
   */
  public function getCommentaireCompoLotMateriel() {
    return $this->commentaireCompoLotMateriel;
  }

  /**
   * Set typeIndividuVocFk
   *
   * @param \App\Entity\Voc $typeIndividuVocFk
   *
   * @return CompositionLotMateriel
   */
  public function setTypeIndividuVocFk(\App\Entity\Voc $typeIndividuVocFk = null) {
    $this->typeIndividuVocFk = $typeIndividuVocFk;

    return $this;
  }

  /**
   * Get typeIndividuVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getTypeIndividuVocFk() {
    return $this->typeIndividuVocFk;
  }

  /**
   * Set lotMaterielFk
   *
   * @param \App\Entity\LotMateriel $lotMaterielFk
   *
   * @return CompositionLotMateriel
   */
  public function setLotMaterielFk(\App\Entity\LotMateriel $lotMaterielFk = null) {
    $this->lotMaterielFk = $lotMaterielFk;

    return $this;
  }

  /**
   * Get lotMaterielFk
   *
   * @return \App\Entity\LotMateriel
   */
  public function getLotMaterielFk() {
    return $this->lotMaterielFk;
  }
}
