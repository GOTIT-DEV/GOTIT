<?php

/*
 * This file is part of the E3sBundle.
 *
 * Copyright (c) 2018 Philippe Grison <philippe.grison@mnhn.fr>
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

/**
 * LotMaterielExtEstRealisePar
 *
 * @ORM\Table(name="lot_materiel_ext_est_realise_par", indexes={@ORM\Index(name="IDX_7D78636FB53CD04C", columns={"personne_fk"}), @ORM\Index(name="IDX_7D78636F40D80ECD", columns={"lot_materiel_ext_fk"})})
 * @ORM\Entity
 */
class LotMaterielExtEstRealisePar
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="lot_materiel_ext_est_realise_par_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

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
     * @var \Personne
     *
     * @ORM\ManyToOne(targetEntity="Personne")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="personne_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $personneFk;

    /**
     * @var \LotMaterielExt
     *
     * @ORM\ManyToOne(targetEntity="LotMaterielExt", inversedBy="lotMaterielExtEstRealisePars")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lot_materiel_ext_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $lotMaterielExtFk;



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
     * @return LotMaterielExtEstRealisePar
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
     * @return LotMaterielExtEstRealisePar
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
     * @return LotMaterielExtEstRealisePar
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
     * @return LotMaterielExtEstRealisePar
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
     * @param \Bbees\E3sBundle\Entity\Personne $personneFk
     *
     * @return LotMaterielExtEstRealisePar
     */
    public function setPersonneFk(\Bbees\E3sBundle\Entity\Personne $personneFk = null)
    {
        $this->personneFk = $personneFk;

        return $this;
    }

    /**
     * Get personneFk
     *
     * @return \Bbees\E3sBundle\Entity\Personne
     */
    public function getPersonneFk()
    {
        return $this->personneFk;
    }

    /**
     * Set lotMaterielExtFk
     *
     * @param \Bbees\E3sBundle\Entity\LotMaterielExt $lotMaterielExtFk
     *
     * @return LotMaterielExtEstRealisePar
     */
    public function setLotMaterielExtFk(\Bbees\E3sBundle\Entity\LotMaterielExt $lotMaterielExtFk = null)
    {
        $this->lotMaterielExtFk = $lotMaterielExtFk;

        return $this;
    }

    /**
     * Get lotMaterielExtFk
     *
     * @return \Bbees\E3sBundle\Entity\LotMaterielExt
     */
    public function getLotMaterielExtFk()
    {
        return $this->lotMaterielExtFk;
    }
}
