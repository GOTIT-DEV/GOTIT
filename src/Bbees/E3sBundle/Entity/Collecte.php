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
 * Collecte
 *
 * @ORM\Table(name="collecte", uniqueConstraints={@ORM\UniqueConstraint(name="cu_collecte_cle_primaire", columns={"code_collecte"})}, indexes={@ORM\Index(name="IDX_55AE4A3DA30C442F", columns={"date_precision_voc_fk"}), @ORM\Index(name="IDX_55AE4A3D50BB334E", columns={"leg_voc_fk"}), @ORM\Index(name="IDX_55AE4A3D369AB36B", columns={"station_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Collecte
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="collecte_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code_collecte", type="string", length=255, nullable=false)
     */
    private $codeCollecte;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_collecte", type="date", nullable=true)
     */
    private $dateCollecte;

    /**
     * @var integer
     *
     * @ORM\Column(name="duree_echantillonnage_mn", type="bigint", nullable=true)
     */
    private $dureeEchantillonnageMn;

    /**
     * @var float
     *
     * @ORM\Column(name="temperature_c", type="float", precision=10, scale=0, nullable=true)
     */
    private $temperatureC;

    /**
     * @var float
     *
     * @ORM\Column(name="conductivite_micro_sie_cm", type="float", precision=10, scale=0, nullable=true)
     */
    private $conductiviteMicroSieCm;

    /**
     * @var integer
     *
     * @ORM\Column(name="a_faire", type="smallint", nullable=false)
     */
    private $aFaire;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire_collecte", type="text", nullable=true)
     */
    private $commentaireCollecte;

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
     *   @ORM\JoinColumn(name="leg_voc_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $legVocFk;

    /**
     * @var \Station
     *
     * @ORM\ManyToOne(targetEntity="Station")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="station_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $stationFk;

    /**
     * @ORM\OneToMany(targetEntity="APourSamplingMethod", mappedBy="collecteFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $aPourSamplingMethods;
    
    /**
     * @ORM\OneToMany(targetEntity="APourFixateur", mappedBy="collecteFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $aPourFixateurs;
    
    /**
     * @ORM\OneToMany(targetEntity="EstFinancePar", mappedBy="collecteFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $estFinancePars;
    
    /**
     * @ORM\OneToMany(targetEntity="EstEffectuePar", mappedBy="collecteFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $estEffectuePars;
    
    /**
     * @ORM\OneToMany(targetEntity="ACibler", mappedBy="collecteFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $aCiblers;
    
    
    
    public function __construct()
    {
        $this->aPourSamplingMethods = new ArrayCollection();
    	$this->aPourFixateurs = new ArrayCollection();
        $this->estFinancePars = new ArrayCollection();
        $this->estEffectuePars = new ArrayCollection();
        $this->aCiblers = new ArrayCollection();
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
     * Set codeCollecte
     *
     * @param string $codeCollecte
     *
     * @return Collecte
     */
    public function setCodeCollecte($codeCollecte)
    {
        $this->codeCollecte = $codeCollecte;

        return $this;
    }

    /**
     * Get codeCollecte
     *
     * @return string
     */
    public function getCodeCollecte()
    {
        return $this->codeCollecte;
    }

    /**
     * Set dateCollecte
     *
     * @param \DateTime $dateCollecte
     *
     * @return Collecte
     */
    public function setDateCollecte($dateCollecte)
    {
        $this->dateCollecte = $dateCollecte;

        return $this;
    }

    /**
     * Get dateCollecte
     *
     * @return \DateTime
     */
    public function getDateCollecte()
    {
        return $this->dateCollecte;
    }

    /**
     * Set dureeEchantillonnageMn
     *
     * @param integer $dureeEchantillonnageMn
     *
     * @return Collecte
     */
    public function setDureeEchantillonnageMn($dureeEchantillonnageMn)
    {
        $this->dureeEchantillonnageMn = $dureeEchantillonnageMn;

        return $this;
    }

    /**
     * Get dureeEchantillonnageMn
     *
     * @return integer
     */
    public function getDureeEchantillonnageMn()
    {
        return $this->dureeEchantillonnageMn;
    }

    /**
     * Set temperatureC
     *
     * @param float $temperatureC
     *
     * @return Collecte
     */
    public function setTemperatureC($temperatureC)
    {
        $this->temperatureC = $temperatureC;

        return $this;
    }

    /**
     * Get temperatureC
     *
     * @return float
     */
    public function getTemperatureC()
    {
        return $this->temperatureC;
    }

    /**
     * Set conductiviteMicroSieCm
     *
     * @param float $conductiviteMicroSieCm
     *
     * @return Collecte
     */
    public function setConductiviteMicroSieCm($conductiviteMicroSieCm)
    {
        $this->conductiviteMicroSieCm = $conductiviteMicroSieCm;

        return $this;
    }

    /**
     * Get conductiviteMicroSieCm
     *
     * @return float
     */
    public function getConductiviteMicroSieCm()
    {
        return $this->conductiviteMicroSieCm;
    }

    /**
     * Set aFaire
     *
     * @param integer $aFaire
     *
     * @return Collecte
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

    /**
     * Set commentaireCollecte
     *
     * @param string $commentaireCollecte
     *
     * @return Collecte
     */
    public function setCommentaireCollecte($commentaireCollecte)
    {
        $this->commentaireCollecte = $commentaireCollecte;

        return $this;
    }

    /**
     * Get commentaireCollecte
     *
     * @return string
     */
    public function getCommentaireCollecte()
    {
        return $this->commentaireCollecte;
    }

    /**
     * Set dateCre
     *
     * @param \DateTime $dateCre
     *
     * @return Collecte
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
     * @return Collecte
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
     * @return Collecte
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
     * @return Collecte
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
     * @return Collecte
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
     * Set legVocFk
     *
     * @param \Bbees\E3sBundle\Entity\Voc $legVocFk
     *
     * @return Collecte
     */
    public function setLegVocFk(\Bbees\E3sBundle\Entity\Voc $legVocFk = null)
    {
        $this->legVocFk = $legVocFk;

        return $this;
    }

    /**
     * Get legVocFk
     *
     * @return \Bbees\E3sBundle\Entity\Voc
     */
    public function getLegVocFk()
    {
        return $this->legVocFk;
    }

    /**
     * Set stationFk
     *
     * @param \Bbees\E3sBundle\Entity\Station $stationFk
     *
     * @return Collecte
     */
    public function setStationFk(\Bbees\E3sBundle\Entity\Station $stationFk = null)
    {
        $this->stationFk = $stationFk;

        return $this;
    }

    /**
     * Get stationFk
     *
     * @return \Bbees\E3sBundle\Entity\Station
     */
    public function getStationFk()
    {
        return $this->stationFk;
    }

    /**
     * Add aPourSamplingMethod
     *
     * @param \Bbees\E3sBundle\Entity\APourSamplingMethod $aPourSamplingMethod
     *
     * @return Collecte
     */
    public function addAPourSamplingMethod(\Bbees\E3sBundle\Entity\APourSamplingMethod $aPourSamplingMethod)
    {
        $aPourSamplingMethod->setCollecteFk($this);
        $this->aPourSamplingMethods[] = $aPourSamplingMethod;

        return $this;
    }

    /**
     * Remove aPourSamplingMethod
     *
     * @param \Bbees\E3sBundle\Entity\APourSamplingMethod $aPourSamplingMethod
     */
    public function removeAPourSamplingMethod(\Bbees\E3sBundle\Entity\APourSamplingMethod $aPourSamplingMethod)
    {
        $this->aPourSamplingMethods->removeElement($aPourSamplingMethod);
    }

    /**
     * Get aPourSamplingMethods
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAPourSamplingMethods()
    {
        return $this->aPourSamplingMethods;
    }

    /**
     * Add aPourFixateur
     *
     * @param \Bbees\E3sBundle\Entity\PourFixateur $aPourFixateur
     *
     * @return Collecte
     */
    public function addAPourFixateur(\Bbees\E3sBundle\Entity\APourFixateur $aPourFixateur)
    {
        $aPourFixateur->setCollecteFk($this);
        $this->aPourFixateurs[] = $aPourFixateur;

        return $this;
    }

    /**
     * Remove aPourFixateur
     *
     * @param \Bbees\E3sBundle\Entity\PourFixateur $aPourFixateur
     */
    public function removeAPourFixateur(\Bbees\E3sBundle\Entity\APourFixateur $aPourFixateur)
    {
        $this->aPourFixateurs->removeElement($aPourFixateur);
    }

    /**
     * Get aPourFixateurs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAPourFixateurs()
    {
        return $this->aPourFixateurs;
    }

    /**
     * Add estFinancePar
     *
     * @param \Bbees\E3sBundle\Entity\EstFinancePar $estFinancePar
     *
     * @return Collecte
     */
    public function addEstFinancePar(\Bbees\E3sBundle\Entity\EstFinancePar $estFinancePar)
    {
        $estFinancePar->setCollecteFk($this);
        $this->estFinancePars[] = $estFinancePar;

        return $this;
    }

    /**
     * Remove estFinancePar
     *
     * @param \Bbees\E3sBundle\Entity\EstFinancePar $estFinancePar
     */
    public function removeEstFinancePar(\Bbees\E3sBundle\Entity\EstFinancePar $estFinancePar)
    {
        $this->estFinancePars->removeElement($estFinancePar);
    }

    /**
     * Get estFinancePars
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEstFinancePars()
    {
        return $this->estFinancePars;
    }

    /**
     * Add estEffectuePar
     *
     * @param \Bbees\E3sBundle\Entity\EstEffectuePar $estEffectuePar
     *
     * @return Collecte
     */
    public function addEstEffectuePar(\Bbees\E3sBundle\Entity\EstEffectuePar $estEffectuePar)
    {
        $estEffectuePar->setCollecteFk($this);
        $this->estEffectuePars[] = $estEffectuePar;

        return $this;
    }

    /**
     * Remove estEffectuePar
     *
     * @param \Bbees\E3sBundle\Entity\EstEffectuePar $estEffectuePar
     */
    public function removeEstEffectuePar(\Bbees\E3sBundle\Entity\EstEffectuePar $estEffectuePar)
    {
        $this->estEffectuePars->removeElement($estEffectuePar);
    }

    /**
     * Get estEffectuePars
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEstEffectuePars()
    {
        return $this->estEffectuePars;
    }

    /**
     * Add aCibler
     *
     * @param \Bbees\E3sBundle\Entity\ACibler $aCibler
     *
     * @return Collecte
     */
    public function addACibler(\Bbees\E3sBundle\Entity\ACibler $aCibler)
    {
        $aCibler->setCollecteFk($this);
        $this->aCiblers[] = $aCibler;

        return $this;
    }

    /**
     * Remove aCibler
     *
     * @param \Bbees\E3sBundle\Entity\ACibler $aCibler
     */
    public function removeACibler(\Bbees\E3sBundle\Entity\ACibler $aCibler)
    {
        $this->aCiblers->removeElement($aCibler);
    }

    /**
     * Get aCiblers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getACiblers()
    {
        return $this->aCiblers;
    }
}
