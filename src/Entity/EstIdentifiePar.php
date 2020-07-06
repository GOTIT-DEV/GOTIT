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
 * EstIdentifiePar
 *
 * @ORM\Table(name="species_is_identified_by", indexes={@ORM\Index(name="IDX_F8FCCF63B53CD04C", columns={"person_fk"}), @ORM\Index(name="IDX_F8FCCF63B4AB6BA0", columns={"identified_species_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class EstIdentifiePar
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="species_is_identified_by_id_seq", allocationSize=1, initialValue=1)
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
     * @var \Personne
     *
     * @ORM\ManyToOne(targetEntity="Personne")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $personneFk;

    /**
     * @var \EspeceIdentifiee
     *
     * @ORM\ManyToOne(targetEntity="EspeceIdentifiee", inversedBy="estIdentifiePars")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="identified_species_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $especeIdentifieeFk;



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
     * @return EstIdentifiePar
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
     * @return EstIdentifiePar
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
     * @return EstIdentifiePar
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
     * @return EstIdentifiePar
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
     * Set personneFk
     *
     * @param \App\Entity\Personne $personneFk
     *
     * @return EstIdentifiePar
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

    /**
     * Set especeIdentifieeFk
     *
     * @param \App\Entity\EspeceIdentifiee $especeIdentifieeFk
     *
     * @return EstIdentifiePar
     */
    public function setEspeceIdentifieeFk(\App\Entity\EspeceIdentifiee $especeIdentifieeFk = null)
    {
        $this->especeIdentifieeFk = $especeIdentifieeFk;

        return $this;
    }

    /**
     * Get especeIdentifieeFk
     *
     * @return \App\Entity\EspeceIdentifiee
     */
    public function getEspeceIdentifieeFk()
    {
        return $this->especeIdentifieeFk;
    }
}
