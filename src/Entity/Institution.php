<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Institution
 *
 * @ORM\Table(name="institution",
 * uniqueConstraints={@ORM\UniqueConstraint(name="uk_institution__institution_name", columns={"institution_name"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"nomEtablissement"}, message="This name already exists")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Institution extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="institution_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="institution_name", type="string", length=1024, nullable=false)
   */
  private $nomEtablissement;

  /**
   * @var string
   *
   * @ORM\Column(name="institution_comments", type="text", nullable=true)
   */
  private $commentaireEtablissement;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set nomEtablissement
   *
   * @param string $nomEtablissement
   *
   * @return Institution
   */
  public function setNomEtablissement($nomEtablissement) {
    $this->nomEtablissement = $nomEtablissement;

    return $this;
  }

  /**
   * Get nomEtablissement
   *
   * @return string
   */
  public function getNomEtablissement() {
    return $this->nomEtablissement;
  }

  /**
   * Set commentaireEtablissement
   *
   * @param string $commentaireEtablissement
   *
   * @return Institution
   */
  public function setCommentaireEtablissement($commentaireEtablissement) {
    $this->commentaireEtablissement = $commentaireEtablissement;

    return $this;
  }

  /**
   * Get commentaireEtablissement
   *
   * @return string
   */
  public function getCommentaireEtablissement() {
    return $this->commentaireEtablissement;
  }
}
