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

/**
 * SourceAEteIntegrePar
 *
 * @ORM\Table(name="source_is_entered_by", indexes={@ORM\Index(name="IDX_16DC6005821B1D3F", columns={"source_fk"}), @ORM\Index(name="IDX_16DC6005B53CD04C", columns={"person_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class SourceAEteIntegrePar
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="source_is_entered_by_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

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
     * @var \Source
     *
     * @ORM\ManyToOne(targetEntity="Source", inversedBy="sourceAEteIntegrePars")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="source_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $sourceFk;

    /**
     * @var \Personne
     *
     * @ORM\ManyToOne(targetEntity="Personne")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $personneFk;



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
     * Set dateCre
     *
     * @param \DateTime $dateCre
     *
     * @return SourceAEteIntegrePar
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
     * @return SourceAEteIntegrePar
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
     * @return SourceAEteIntegrePar
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
     * @return SourceAEteIntegrePar
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
     * Set sourceFk
     *
     * @param \App\Entity\Source $sourceFk
     *
     * @return SourceAEteIntegrePar
     */
    public function setSourceFk(\App\Entity\Source $sourceFk = null)
    {
        $this->sourceFk = $sourceFk;

        return $this;
    }

    /**
     * Get sourceFk
     *
     * @return \App\Entity\Source
     */
    public function getSourceFk()
    {
        return $this->sourceFk;
    }

    /**
     * Set personneFk
     *
     * @param \App\Entity\Personne $personneFk
     *
     * @return SourceAEteIntegrePar
     */
    public function setPersonneFk(\App\Entity\Personne $personneFk = null)
    {
        $this->personneFk = $personneFk;

        return $this;
    }

    /**
     * Get personneFk
     *
     * @return \App\Entity\Personne
     */
    public function getPersonneFk()
    {
        return $this->personneFk;
    }
}
