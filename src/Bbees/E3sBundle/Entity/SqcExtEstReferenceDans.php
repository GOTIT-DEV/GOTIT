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
 * SqcExtEstReferenceDans
 *
 * @ORM\Table(name="sqc_ext_est_reference_dans", indexes={@ORM\Index(name="IDX_8D0E8D6A821B1D3F", columns={"source_fk"}), @ORM\Index(name="IDX_8D0E8D6ACDD1F756", columns={"sequence_assemblee_ext_fk"})})
 * @ORM\Entity
 */
class SqcExtEstReferenceDans
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="sqc_ext_est_reference_dans_id_seq", allocationSize=1, initialValue=1)
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
     * @var \Source
     *
     * @ORM\ManyToOne(targetEntity="Source")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="source_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $sourceFk;

    /**
     * @var \SequenceAssembleeExt
     *
     * @ORM\ManyToOne(targetEntity="SequenceAssembleeExt", inversedBy="sqcExtEstReferenceDanss")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sequence_assemblee_ext_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $sequenceAssembleeExtFk;



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
     * @return SqcExtEstReferenceDans
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
     * @return SqcExtEstReferenceDans
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
     * @return SqcExtEstReferenceDans
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
     * @return SqcExtEstReferenceDans
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
     * @param \Bbees\E3sBundle\Entity\Source $sourceFk
     *
     * @return SqcExtEstReferenceDans
     */
    public function setSourceFk(\Bbees\E3sBundle\Entity\Source $sourceFk = null)
    {
        $this->sourceFk = $sourceFk;

        return $this;
    }

    /**
     * Get sourceFk
     *
     * @return \Bbees\E3sBundle\Entity\Source
     */
    public function getSourceFk()
    {
        return $this->sourceFk;
    }

    /**
     * Set sequenceAssembleeExtFk
     *
     * @param \Bbees\E3sBundle\Entity\SequenceAssembleeExt $sequenceAssembleeExtFk
     *
     * @return SqcExtEstReferenceDans
     */
    public function setSequenceAssembleeExtFk(\Bbees\E3sBundle\Entity\SequenceAssembleeExt $sequenceAssembleeExtFk = null)
    {
        $this->sequenceAssembleeExtFk = $sequenceAssembleeExtFk;

        return $this;
    }

    /**
     * Get sequenceAssembleeExtFk
     *
     * @return \Bbees\E3sBundle\Entity\SequenceAssembleeExt
     */
    public function getSequenceAssembleeExtFk()
    {
        return $this->sequenceAssembleeExtFk;
    }
}
