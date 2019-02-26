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

namespace Bbees\E3sBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LotMateriel
 *
 * @ORM\Table(name="lot_materiel", uniqueConstraints={@ORM\UniqueConstraint(name="cu_lot_materiel_cle_primaire", columns={"code_lot_materiel"})}, indexes={@ORM\Index(name="IDX_BA1841A5A30C442F", columns={"date_precision_voc_fk"}), @ORM\Index(name="IDX_BA1841A5B0B56B73", columns={"pigmentation_voc_fk"}), @ORM\Index(name="IDX_BA1841A5A897CC9E", columns={"yeux_voc_fk"}), @ORM\Index(name="IDX_BA1841A5662D9B98", columns={"collecte_fk"}), @ORM\Index(name="IDX_BA1841A52B644673", columns={"boite_fk"})})
 * @ORM\Entity
 */ 
class LotMateriel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="lot_materiel_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code_lot_materiel", type="string", length=255, nullable=false)
     */
    private $codeLotMateriel;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_lot_materiel", type="date", nullable=true)
     */
    private $dateLotMateriel;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire_conseil_sqc", type="text", nullable=true)
     */
    private $commentaireConseilSqc;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire_lot_materiel", type="text", nullable=true)
     */
    private $commentaireLotMateriel;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="a_faire", type="smallint", nullable=false)
     */
    private $aFaire;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_cre", type="datetime", nullable=true)
     */
    private $dateCre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_maj", type="datetime", nullable=true)
     */
    private $dateMaj;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_cre", type="bigint", nullable=true)
     */
    private $userCre;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_maj", type="bigint", nullable=true)
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
     *   @ORM\JoinColumn(name="yeux_voc_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $yeuxVocFk;

    /**
     * @var \Collecte
     *
     * @ORM\ManyToOne(targetEntity="Collecte")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="collecte_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $collecteFk;

    /**
     * @var \Boite
     *
     * @ORM\ManyToOne(targetEntity="Boite", inversedBy="lotMateriels")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="boite_fk", referencedColumnName="id", nullable=true)
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
     * @param \Bbees\E3sBundle\Entity\Voc $datePrecisionVocFk
     *
     * @return LotMateriel
     */
    public function setDatePrecisionVocFk(\Bbees\E3sBundle\Entity\Voc $datePrecisionVocFk = null)
    {
        $this->datePrecisionVocFk = $datePrecisionVocFk;

        return $this;
    }

    /**
     * Get datePrecisionVocFk
     *
     * @return \Bbees\E3sBundle\Entity\Voc
     */
    public function getDatePrecisionVocFk()
    {
        return $this->datePrecisionVocFk;
    }

    /**
     * Set pigmentationVocFk
     *
     * @param \Bbees\E3sBundle\Entity\Voc $pigmentationVocFk
     *
     * @return LotMateriel
     */
    public function setPigmentationVocFk(\Bbees\E3sBundle\Entity\Voc $pigmentationVocFk = null)
    {
        $this->pigmentationVocFk = $pigmentationVocFk;

        return $this;
    }

    /**
     * Get pigmentationVocFk
     *
     * @return \Bbees\E3sBundle\Entity\Voc
     */
    public function getPigmentationVocFk()
    {
        return $this->pigmentationVocFk;
    }

    /**
     * Set yeuxVocFk
     *
     * @param \Bbees\E3sBundle\Entity\Voc $yeuxVocFk
     *
     * @return LotMateriel
     */
    public function setYeuxVocFk(\Bbees\E3sBundle\Entity\Voc $yeuxVocFk = null)
    {
        $this->yeuxVocFk = $yeuxVocFk;

        return $this;
    }

    /**
     * Get yeuxVocFk
     *
     * @return \Bbees\E3sBundle\Entity\Voc
     */
    public function getYeuxVocFk()
    {
        return $this->yeuxVocFk;
    }

    /**
     * Set collecteFk
     *
     * @param \Bbees\E3sBundle\Entity\Collecte $collecteFk
     *
     * @return LotMateriel
     */
    public function setCollecteFk(\Bbees\E3sBundle\Entity\Collecte $collecteFk = null)
    {
        $this->collecteFk = $collecteFk;

        return $this;
    }

    /**
     * Get collecteFk
     *
     * @return \Bbees\E3sBundle\Entity\Collecte
     */
    public function getCollecteFk()
    {
        return $this->collecteFk;
    }

    /**
     * Set boiteFk
     *
     * @param \Bbees\E3sBundle\Entity\Boite $boiteFk
     *
     * @return LotMateriel
     */
    public function setBoiteFk(\Bbees\E3sBundle\Entity\Boite $boiteFk = null)
    {
        $this->boiteFk = $boiteFk;

        return $this;
    }

    /**
     * Get boiteFk
     *
     * @return \Bbees\E3sBundle\Entity\Boite
     */
    public function getBoiteFk()
    {
        return $this->boiteFk;
    }

    /**
     * Add lotMaterielEstRealisePar
     *
     * @param \Bbees\E3sBundle\Entity\LotMaterielEstRealisePar $lotMaterielEstRealisePar
     *
     * @return LotMateriel
     */
    public function addLotMaterielEstRealisePar(\Bbees\E3sBundle\Entity\LotMaterielEstRealisePar $lotMaterielEstRealisePar)
    {
        $lotMaterielEstRealisePar->setLotMaterielFk($this);
        $this->lotMaterielEstRealisePars[] = $lotMaterielEstRealisePar;

        return $this;
    }

    /**
     * Remove lotMaterielEstRealisePar
     *
     * @param \Bbees\E3sBundle\Entity\LotMaterielEstRealisePar $lotMaterielEstRealisePar
     */
    public function removeLotMaterielEstRealisePar(\Bbees\E3sBundle\Entity\LotMaterielEstRealisePar $lotMaterielEstRealisePar)
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
     * @param \Bbees\E3sBundle\Entity\LotEstPublieDans $lotEstPublieDanss
     *
     * @return LotMateriel
     */
    public function addLotEstPublieDans(\Bbees\E3sBundle\Entity\LotEstPublieDans $lotEstPublieDanss)
    {
       
        $lotEstPublieDanss->setLotMaterielFk($this);
        $this->lotEstPublieDanss[] = $lotEstPublieDanss;

        return $this;
    }

    /**
     * Remove lotEstPublieDanss
     *
     * @param \Bbees\E3sBundle\Entity\LotEstPublieDans $lotEstPublieDanss
     */
    public function removeLotEstPublieDans(\Bbees\E3sBundle\Entity\LotEstPublieDans $lotEstPublieDanss)
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
     * @param \Bbees\E3sBundle\Entity\EspeceIdentifiee $especeIdentifiee
     *
     * @return LotMateriel
     */
    public function addEspeceIdentifiee(\Bbees\E3sBundle\Entity\EspeceIdentifiee $especeIdentifiee)
    {
        $especeIdentifiee->setLotMaterielFk($this);
        $this->especeIdentifiees[] = $especeIdentifiee;

        return $this;
    }

    /**
     * Remove especeIdentifiee
     *
     * @param \Bbees\E3sBundle\Entity\EspeceIdentifiee $especeIdentifiee
     */
    public function removeEspeceIdentifiee(\Bbees\E3sBundle\Entity\EspeceIdentifiee $especeIdentifiee)
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
     * @param \Bbees\E3sBundle\Entity\CompositionLotMateriel $compositionLotMateriel
     *
     * @return LotMateriel
     */
    public function addCompositionLotMateriel(\Bbees\E3sBundle\Entity\CompositionLotMateriel $compositionLotMateriel)
    {
        $compositionLotMateriel->setLotMaterielFk($this);
        $this->compositionLotMateriels[] = $compositionLotMateriel;

        return $this;
    }

    /**
     * Remove compositionLotMateriel
     *
     * @param \Bbees\E3sBundle\Entity\CompositionLotMateriel $compositionLotMateriel
     */
    public function removeCompositionLotMateriel(\Bbees\E3sBundle\Entity\CompositionLotMateriel $compositionLotMateriel)
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
     * @param \Bbees\E3sBundle\Entity\LotEstPublieDans $lotEstPublieDanss
     *
     * @return LotMateriel
     */
    public function addLotEstPublieDanss(\Bbees\E3sBundle\Entity\LotEstPublieDans $lotEstPublieDanss)
    {
        $this->lotEstPublieDanss[] = $lotEstPublieDanss;

        return $this;
    }

    /**
     * Remove lotEstPublieDanss
     *
     * @param \Bbees\E3sBundle\Entity\LotEstPublieDans $lotEstPublieDanss
     */
    public function removeLotEstPublieDanss(\Bbees\E3sBundle\Entity\LotEstPublieDans $lotEstPublieDanss)
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
