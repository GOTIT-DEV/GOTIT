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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Station
 *
 * @ORM\Table(name="site", uniqueConstraints={@ORM\UniqueConstraint(name="uk_site__site_code", columns={"site_code"})}, indexes={@ORM\Index(name="IDX_9F39F8B143D4E2C", columns={"municipality_fk"}), @ORM\Index(name="IDX_9F39F8B1B1C3431A", columns={"country_fk"}), @ORM\Index(name="IDX_9F39F8B14D50D031", columns={"access_point_voc_fk"}), @ORM\Index(name="IDX_9F39F8B1C23046AE", columns={"habitat_type_voc_fk"}), @ORM\Index(name="IDX_9F39F8B1E86DBD90", columns={"coordinate_precision_voc_fk"})})
 * @ORM\Entity
 * @UniqueEntity(
 *  fields={"codeStation"},
 *  message="This code is already registered"
 * )
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Station
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="site_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="site_code", type="string", length=255, nullable=false)
     */
    private $codeStation;

    /**
     * @var string
     *
     * @ORM\Column(name="site_name", type="string", length=1024, nullable=false)
     */
    private $nomStation;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float", precision=10, scale=0, nullable=false)
     */
    private $latDegDec;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float", precision=10, scale=0, nullable=false)
     */
    private $longDegDec;

    /**
     * @var integer
     *
     * @ORM\Column(name="elevation", type="bigint", nullable=true)
     */
    private $altitudeM;

    /**
     * @var string
     *
     * @ORM\Column(name="location_info", type="text", nullable=true)
     */
    private $infoLocalisation;

    /**
     * @var string
     *
     * @ORM\Column(name="site_description", type="text", nullable=true)
     */
    private $infoDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="site_comments", type="text", nullable=true)
     */
    private $commentaireStation;

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
     * @var \Commune
     *
     * @ORM\ManyToOne(targetEntity="Commune")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="municipality_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $communeFk;

    /**
     * @var \Pays
     *
     * @ORM\ManyToOne(targetEntity="Pays")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="country_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $paysFk;

    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="access_point_voc_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $pointAccesVocFk;

    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="habitat_type_voc_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $habitatTypeVocFk;

    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="coordinate_precision_voc_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $precisionLatLongVocFk;



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
     * Set codeStation
     *
     * @param string $codeStation
     *
     * @return Station
     */
    public function setCodeStation($codeStation)
    {
        $this->codeStation = $codeStation;

        return $this;
    }

    /**
     * Get codeStation
     *
     * @return string
     */
    public function getCodeStation()
    {
        return $this->codeStation;
    }

    /**
     * Set nomStation
     *
     * @param string $nomStation
     *
     * @return Station
     */
    public function setNomStation($nomStation)
    {
        $this->nomStation = $nomStation;

        return $this;
    }

    /**
     * Get nomStation
     *
     * @return string
     */
    public function getNomStation()
    {
        return $this->nomStation;
    }

    /**
     * Set latDegDec
     *
     * @param float $latDegDec
     *
     * @return Station
     */
    public function setLatDegDec($latDegDec)
    {
        $this->latDegDec = $latDegDec;

        return $this;
    }

    /**
     * Get latDegDec
     *
     * @return float
     */
    public function getLatDegDec()
    {
        return $this->latDegDec;
    }

    /**
     * Set longDegDec
     *
     * @param float $longDegDec
     *
     * @return Station
     */
    public function setLongDegDec($longDegDec)
    {
        $this->longDegDec = $longDegDec;

        return $this;
    }

    /**
     * Get longDegDec
     *
     * @return float
     */
    public function getLongDegDec()
    {
        return $this->longDegDec;
    }

    /**
     * Set altitudeM
     *
     * @param integer $altitudeM
     *
     * @return Station
     */
    public function setAltitudeM($altitudeM)
    {
        $this->altitudeM = $altitudeM;

        return $this;
    }

    /**
     * Get altitudeM
     *
     * @return integer
     */
    public function getAltitudeM()
    {
        return $this->altitudeM;
    }

    /**
     * Set infoLocalisation
     *
     * @param string $infoLocalisation
     *
     * @return Station
     */
    public function setInfoLocalisation($infoLocalisation)
    {
        $this->infoLocalisation = $infoLocalisation;

        return $this;
    }

    /**
     * Get infoLocalisation
     *
     * @return string
     */
    public function getInfoLocalisation()
    {
        return $this->infoLocalisation;
    }

    /**
     * Set infoDescription
     *
     * @param string $infoDescription
     *
     * @return Station
     */
    public function setInfoDescription($infoDescription)
    {
        $this->infoDescription = $infoDescription;

        return $this;
    }

    /**
     * Get infoDescription
     *
     * @return string
     */
    public function getInfoDescription()
    {
        return $this->infoDescription;
    }

    /**
     * Set commentaireStation
     *
     * @param string $commentaireStation
     *
     * @return Station
     */
    public function setCommentaireStation($commentaireStation)
    {
        $this->commentaireStation = $commentaireStation;

        return $this;
    }

    /**
     * Get commentaireStation
     *
     * @return string
     */
    public function getCommentaireStation()
    {
        return $this->commentaireStation;
    }

    /**
     * Set dateCre
     *
     * @param \DateTime $dateCre
     *
     * @return Station
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
     * @return Station
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
     * @return Station
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
     * @return Station
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
     * Set communeFk
     *
     * @param \App\Entity\Commune $communeFk
     *
     * @return Station
     */
    public function setCommuneFk(\App\Entity\Commune $communeFk = null)
    {
        $this->communeFk = $communeFk;

        return $this;
    }

    /**
     * Get communeFk
     *
     * @return \App\Entity\Commune
     */
    public function getCommuneFk()
    {
        return $this->communeFk;
    }

    /**
     * Set paysFk
     *
     * @param \App\Entity\Pays $paysFk
     *
     * @return Station
     */
    public function setPaysFk(\App\Entity\Pays $paysFk = null)
    {
        $this->paysFk = $paysFk;

        return $this;
    }

    /**
     * Get paysFk
     *
     * @return \App\Entity\Pays
     */
    public function getPaysFk()
    {
        return $this->paysFk;
    }

    /**
     * Set pointAccesVocFk
     *
     * @param \App\Entity\Voc $pointAccesVocFk
     *
     * @return Station
     */
    public function setPointAccesVocFk(\App\Entity\Voc $pointAccesVocFk = null)
    {
        $this->pointAccesVocFk = $pointAccesVocFk;

        return $this;
    }

    /**
     * Get pointAccesVocFk
     *
     * @return \App\Entity\Voc
     */
    public function getPointAccesVocFk()
    {
        return $this->pointAccesVocFk;
    }

    /**
     * Set habitatTypeVocFk
     *
     * @param \App\Entity\Voc $habitatTypeVocFk
     *
     * @return Station
     */
    public function setHabitatTypeVocFk(\App\Entity\Voc $habitatTypeVocFk = null)
    {
        $this->habitatTypeVocFk = $habitatTypeVocFk;

        return $this;
    }

    /**
     * Get habitatTypeVocFk
     *
     * @return \App\Entity\Voc
     */
    public function getHabitatTypeVocFk()
    {
        return $this->habitatTypeVocFk;
    }

    /**
     * Set precisionLatLongVocFk
     *
     * @param \App\Entity\Voc $precisionLatLongVocFk
     *
     * @return Station
     */
    public function setPrecisionLatLongVocFk(\App\Entity\Voc $precisionLatLongVocFk = null)
    {
        $this->precisionLatLongVocFk = $precisionLatLongVocFk;

        return $this;
    }

    /**
     * Get precisionLatLongVocFk
     *
     * @return \App\Entity\Voc
     */
    public function getPrecisionLatLongVocFk()
    {
        return $this->precisionLatLongVocFk;
    }
}
