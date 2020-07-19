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
 * Commune
 *
 * @ORM\Table(name="municipality", 
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_municipality__municipality_code", columns={"municipality_code"})}, 
 *  indexes={@ORM\Index(name="IDX_E2E2D1EEB1C3431A", columns={"country_fk"})})
 * @ORM\Entity
 * @UniqueEntity(
 *  fields={"codeCommune"},
 *  message="This code is already registered"
 * )
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Commune
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="municipality_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="municipality_code", type="string", length=255, nullable=false)
     */
    private $codeCommune;

    /**
     * @var string
     *
     * @ORM\Column(name="municipality_name", type="string", length=1024, nullable=false)
     */
    private $nomCommune;

    /**
     * @var string
     *
     * @ORM\Column(name="region_name", type="string", length=1024, nullable=false)
     */
    private $nomRegion;

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
     * @var \Pays
     *
     * @ORM\ManyToOne(targetEntity="Pays")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="country_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $paysFk;



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
     * Set codeCommune
     *
     * @param string $codeCommune
     *
     * @return Commune
     */
    public function setCodeCommune($codeCommune)
    {
        $this->codeCommune = $codeCommune;

        return $this;
    }

    /**
     * Get codeCommune
     *
     * @return string
     */
    public function getCodeCommune()
    {
        return $this->codeCommune;
    }

    /**
     * Set nomCommune
     *
     * @param string $nomCommune
     *
     * @return Commune
     */
    public function setNomCommune($nomCommune)
    {
        $this->nomCommune = $nomCommune;

        return $this;
    }

    /**
     * Get nomCommune
     *
     * @return string
     */
    public function getNomCommune()
    {
        return $this->nomCommune;
    }

    /**
     * Set nomRegion
     *
     * @param string $nomRegion
     *
     * @return Commune
     */
    public function setNomRegion($nomRegion)
    {
        $this->nomRegion = $nomRegion;

        return $this;
    }

    /**
     * Get nomRegion
     *
     * @return string
     */
    public function getNomRegion()
    {
        return $this->nomRegion;
    }

    /**
     * Set dateCre
     *
     * @param \DateTime $dateCre
     *
     * @return Commune
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
     * @return Commune
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
     * @return Commune
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
     * @return Commune
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
     * Set paysFk
     *
     * @param \App\Entity\Pays $paysFk
     *
     * @return Commune
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
}
