<?php

/*
 * This file is part of the E3sBundle.
 *
 * Authors : see information concerning authors of GOTIT project in file AUTHORS.md
 *
 * E3sBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * E3sBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with E3sBundle.  If not, see <https://www.gnu.org/licenses/>
 * 
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * LotMateriel
 *
 * @ORM\Table(name="internal_biological_material", 
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_internal_biological_material__internal_biological_material_c", columns={"internal_biological_material_code"})}, 
 *  indexes={
 *      @ORM\Index(name="IDX_BA1841A5A30C442F", columns={"date_precision_voc_fk"}), 
 *      @ORM\Index(name="IDX_BA1841A5B0B56B73", columns={"pigmentation_voc_fk"}), 
 *      @ORM\Index(name="IDX_BA1841A5A897CC9E", columns={"eyes_voc_fk"}), 
 *      @ORM\Index(name="IDX_BA1841A5662D9B98", columns={"sampling_fk"}), 
 *      @ORM\Index(name="IDX_BA1841A52B644673", columns={"storage_box_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"codeLotMateriel"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class LotMateriel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="internal_biological_material_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="internal_biological_material_code", type="string", length=255, nullable=false)
     */
    private $codeLotMateriel;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="internal_biological_material_date", type="date", nullable=true)
     */
    private $dateLotMateriel;

    /**
     * @var string
     *
     * @ORM\Column(name="sequencing_advice", type="text", nullable=true)
     */
    private $commentaireConseilSqc;

    /**
     * @var string
     *
     * @ORM\Column(name="internal_biological_material_comments", type="text", nullable=true)
     */
    private $commentaireLotMateriel;

    /**
     * @var integer
     *
     * @ORM\Column(name="internal_biological_material_status", type="smallint", nullable=false)
     */
    private $aFaire;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_of_creation", type="datetime", nullable=true)
     */
    private $dateCre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_of_update", type="datetime", nullable=true)
     */
    private $dateMaj;

    /**
     * @var integer
     *
     * @ORM\Column(name="creation_user_name", type="bigint", nullable=true)
     */
    private $userCre;

    /**
     * @var integer
     *
     * @ORM\Column(name="update_user_name", type="bigint", nullable=true)
     */
    private $userMaj;

    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="date_precision_voc_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $datePrecisionVocFk;

    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pigmentation_voc_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $pigmentationVocFk;

    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="eyes_voc_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $yeuxVocFk;

    /**
     * @var \Collecte
     *
     * @ORM\ManyToOne(targetEntity="Collecte")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sampling_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $collecteFk;

    /**
     * @var \Boite
     *
     * @ORM\ManyToOne(targetEntity="Boite", inversedBy="lotMateriels")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="storage_box_fk", referencedColumnName="id", nullable=true)
     * })
     */
    private $boiteFk;

    /**
     * @ORM\OneToMany(targetEntity="LotMaterielEstRealisePar", mappedBy="lotMaterielFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $lotMaterielEstRealisePars;

    /**
     * @ORM\OneToMany(targetEntity="LotEstPublieDans", mappedBy="lotMaterielFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $lotEstPublieDanss;

    /**
     * @ORM\OneToMany(targetEntity="EspeceIdentifiee", mappedBy="lotMaterielFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $especeIdentifiees;

    /**
     * @ORM\OneToMany(targetEntity="CompositionLotMateriel", mappedBy="lotMaterielFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $compositionLotMateriels;


    public function __construct()
    {
        $this->lotMaterielEstRealisePars = new ArrayCollection();
        $this->lotEstPublieDanss = new ArrayCollection();
        $this->especeIdentifiees = new ArrayCollection();
        $this->compositionLotMateriels = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set codeLotMateriel
     *
     * @param string $codeLotMateriel
     *
     * @return LotMateriel
     */
    public function setCodeLotMateriel($codeLotMateriel)
    {
        $this->codeLotMateriel = $codeLotMateriel;

        return $this;
    }

    /**
     * Get codeLotMateriel
     *
     * @return string
     */
    public function getCodeLotMateriel()
    {
        return $this->codeLotMateriel;
    }

    /**
     * Set dateLotMateriel
     *
     * @param \DateTime $dateLotMateriel
     *
     * @return LotMateriel
     */
    public function setDateLotMateriel($dateLotMateriel)
    {
        $this->dateLotMateriel = $dateLotMateriel;

        return $this;
    }

    /**
     * Get dateLotMateriel
     *
     * @return \DateTime
     */
    public function getDateLotMateriel()
    {
        return $this->dateLotMateriel;
    }

    /**
     * Set commentaireConseilSqc
     *
     * @param string $commentaireConseilSqc
     *
     * @return LotMateriel
     */
    public function setCommentaireConseilSqc($commentaireConseilSqc)
    {
        $this->commentaireConseilSqc = $commentaireConseilSqc;

        return $this;
    }

    /**
     * Get commentaireConseilSqc
     *
     * @return string
     */
    public function getCommentaireConseilSqc()
    {
        return $this->commentaireConseilSqc;
    }

    /**
     * Set commentaireLotMateriel
     *
     * @param string $commentaireLotMateriel
     *
     * @return LotMateriel
     */
    public function setCommentaireLotMateriel($commentaireLotMateriel)
    {
        $this->commentaireLotMateriel = $commentaireLotMateriel;

        return $this;
    }

    /**
     * Get commentaireLotMateriel
     *
     * @return string
     */
    public function getCommentaireLotMateriel()
    {
        return $this->commentaireLotMateriel;
    }

    /**
     * Set dateCre
     *
     * @param \DateTime $dateCre
     *
     * @return LotMateriel
     */
    public function setDateCre($dateCre)
    {
        $this->dateCre = $dateCre;

        return $this;
    }

    /**
     * Get dateCre
     *
     * @return \DateTime
     */
    public function getDateCre()
    {
        return $this->dateCre;
    }

    /**
     * Set dateMaj
     *
     * @param \DateTime $dateMaj
     *
     * @return LotMateriel
     */
    public function setDateMaj($dateMaj)
    {
        $this->dateMaj = $dateMaj;

        return $this;
    }

    /**
     * Get dateMaj
     *
     * @return \DateTime
     */
    public function getDateMaj()
    {
        return $this->dateMaj;
    }

    /**
     * Set userCre
     *
     * @param integer $userCre
     *
     * @return LotMateriel
     */
    public function setUserCre($userCre)
    {
        $this->userCre = $userCre;

        return $this;
    }

    /**
     * Get userCre
     *
     * @return integer
     */
    public function getUserCre()
    {
        return $this->userCre;
    }

    /**
     * Set userMaj
     *
     * @param integer $userMaj
     *
     * @return LotMateriel
     */
    public function setUserMaj($userMaj)
    {
        $this->userMaj = $userMaj;

        return $this;
    }

    /**
     * Get userMaj
     *
     * @return integer
     */
    public function getUserMaj()
    {
        return $this->userMaj;
    }

    /**
     * Set datePrecisionVocFk
     *
     * @param \App\Entity\Voc $datePrecisionVocFk
     *
     * @return LotMateriel
     */
    public function setDatePrecisionVocFk(\App\Entity\Voc $datePrecisionVocFk = null)
    {
        $this->datePrecisionVocFk = $datePrecisionVocFk;

        return $this;
    }

    /**
     * Get datePrecisionVocFk
     *
     * @return \App\Entity\Voc
     */
    public function getDatePrecisionVocFk()
    {
        return $this->datePrecisionVocFk;
    }

    /**
     * Set pigmentationVocFk
     *
     * @param \App\Entity\Voc $pigmentationVocFk
     *
     * @return LotMateriel
     */
    public function setPigmentationVocFk(\App\Entity\Voc $pigmentationVocFk = null)
    {
        $this->pigmentationVocFk = $pigmentationVocFk;

        return $this;
    }

    /**
     * Get pigmentationVocFk
     *
     * @return \App\Entity\Voc
     */
    public function getPigmentationVocFk()
    {
        return $this->pigmentationVocFk;
    }

    /**
     * Set yeuxVocFk
     *
     * @param \App\Entity\Voc $yeuxVocFk
     *
     * @return LotMateriel
     */
    public function setYeuxVocFk(\App\Entity\Voc $yeuxVocFk = null)
    {
        $this->yeuxVocFk = $yeuxVocFk;

        return $this;
    }

    /**
     * Get yeuxVocFk
     *
     * @return \App\Entity\Voc
     */
    public function getYeuxVocFk()
    {
        return $this->yeuxVocFk;
    }

    /**
     * Set collecteFk
     *
     * @param \App\Entity\Collecte $collecteFk
     *
     * @return LotMateriel
     */
    public function setCollecteFk(\App\Entity\Collecte $collecteFk = null)
    {
        $this->collecteFk = $collecteFk;

        return $this;
    }

    /**
     * Get collecteFk
     *
     * @return \App\Entity\Collecte
     */
    public function getCollecteFk()
    {
        return $this->collecteFk;
    }

    /**
     * Set boiteFk
     *
     * @param \App\Entity\Boite $boiteFk
     *
     * @return LotMateriel
     */
    public function setBoiteFk(\App\Entity\Boite $boiteFk = null)
    {
        $this->boiteFk = $boiteFk;

        return $this;
    }

    /**
     * Get boiteFk
     *
     * @return \App\Entity\Boite
     */
    public function getBoiteFk()
    {
        return $this->boiteFk;
    }

    /**
     * Add lotMaterielEstRealisePar
     *
     * @param \App\Entity\LotMaterielEstRealisePar $lotMaterielEstRealisePar
     *
     * @return LotMateriel
     */
    public function addLotMaterielEstRealisePar(\App\Entity\LotMaterielEstRealisePar $lotMaterielEstRealisePar)
    {
        $lotMaterielEstRealisePar->setLotMaterielFk($this);
        $this->lotMaterielEstRealisePars[] = $lotMaterielEstRealisePar;

        return $this;
    }

    /**
     * Remove lotMaterielEstRealisePar
     *
     * @param \App\Entity\LotMaterielEstRealisePar $lotMaterielEstRealisePar
     */
    public function removeLotMaterielEstRealisePar(\App\Entity\LotMaterielEstRealisePar $lotMaterielEstRealisePar)
    {
        $this->lotMaterielEstRealisePars->removeElement($lotMaterielEstRealisePar);
    }

    /**
     * Get lotMaterielEstRealisePars
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLotMaterielEstRealisePars()
    {
        return $this->lotMaterielEstRealisePars;
    }

    /**
     * Add lotEstPublieDanss
     *
     * @param \App\Entity\LotEstPublieDans $lotEstPublieDanss
     *
     * @return LotMateriel
     */
    public function addLotEstPublieDans(\App\Entity\LotEstPublieDans $lotEstPublieDanss)
    {

        $lotEstPublieDanss->setLotMaterielFk($this);
        $this->lotEstPublieDanss[] = $lotEstPublieDanss;

        return $this;
    }

    /**
     * Remove lotEstPublieDanss
     *
     * @param \App\Entity\LotEstPublieDans $lotEstPublieDanss
     */
    public function removeLotEstPublieDans(\App\Entity\LotEstPublieDans $lotEstPublieDanss)
    {
        $this->lotEstPublieDanss->removeElement($lotEstPublieDanss);
    }

    /**
     * Get lotEstPublieDanss
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLotEstPublieDanss()
    {
        return $this->lotEstPublieDanss;
    }

    /**
     * Add especeIdentifiee
     *
     * @param \App\Entity\EspeceIdentifiee $especeIdentifiee
     *
     * @return LotMateriel
     */
    public function addEspeceIdentifiee(\App\Entity\EspeceIdentifiee $especeIdentifiee)
    {
        $especeIdentifiee->setLotMaterielFk($this);
        $this->especeIdentifiees[] = $especeIdentifiee;

        return $this;
    }

    /**
     * Remove especeIdentifiee
     *
     * @param \App\Entity\EspeceIdentifiee $especeIdentifiee
     */
    public function removeEspeceIdentifiee(\App\Entity\EspeceIdentifiee $especeIdentifiee)
    {
        $this->especeIdentifiees->removeElement($especeIdentifiee);
    }

    /**
     * Get especeIdentifiees
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEspeceIdentifiees()
    {
        return $this->especeIdentifiees;
    }

    /**
     * Add compositionLotMateriel
     *
     * @param \App\Entity\CompositionLotMateriel $compositionLotMateriel
     *
     * @return LotMateriel
     */
    public function addCompositionLotMateriel(\App\Entity\CompositionLotMateriel $compositionLotMateriel)
    {
        $compositionLotMateriel->setLotMaterielFk($this);
        $this->compositionLotMateriels[] = $compositionLotMateriel;

        return $this;
    }

    /**
     * Remove compositionLotMateriel
     *
     * @param \App\Entity\CompositionLotMateriel $compositionLotMateriel
     */
    public function removeCompositionLotMateriel(\App\Entity\CompositionLotMateriel $compositionLotMateriel)
    {
        $this->compositionLotMateriels->removeElement($compositionLotMateriel);
    }

    /**
     * Get compositionLotMateriels
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompositionLotMateriels()
    {
        return $this->compositionLotMateriels;
    }

    /**
     * Add lotEstPublieDanss
     *
     * @param \App\Entity\LotEstPublieDans $lotEstPublieDanss
     *
     * @return LotMateriel
     */
    public function addLotEstPublieDanss(\App\Entity\LotEstPublieDans $lotEstPublieDanss)
    {
        $this->lotEstPublieDanss[] = $lotEstPublieDanss;

        return $this;
    }

    /**
     * Remove lotEstPublieDanss
     *
     * @param \App\Entity\LotEstPublieDans $lotEstPublieDanss
     */
    public function removeLotEstPublieDanss(\App\Entity\LotEstPublieDans $lotEstPublieDanss)
    {
        $this->lotEstPublieDanss->removeElement($lotEstPublieDanss);
    }

    /**
     * Set aFaire
     *
     * @param integer $aFaire
     *
     * @return LotMateriel
     */
    public function setAFaire($aFaire)
    {
        $this->aFaire = $aFaire;

        return $this;
    }

    /**
     * Get aFaire
     *
     * @return integer
     */
    public function getAFaire()
    {
        return $this->aFaire;
    }
}
