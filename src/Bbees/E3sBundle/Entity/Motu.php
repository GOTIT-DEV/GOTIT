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
 * Motu
 *
 * @ORM\Table(name="motu")
 * @ORM\Entity
 */
class Motu
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="motu_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="libelle_motu", type="string", length=255, nullable=false)
     */
    private $libelleMotu;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_fichier_csv", type="string", length=1024, nullable=false)
     */
    private $nomFichierCsv;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_motu", type="date", nullable=false)
     */
    private $dateMotu;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire_motu", type="text", nullable=true)
     */
    private $commentaireMotu;

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
     * @ORM\OneToMany(targetEntity="MotuEstGenerePar", mappedBy="motuFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $motuEstGenerePars;
    
    
    public function __construct()
    {
        $this->motuEstGenerePars = new ArrayCollection();
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
     * Set nomFichierCsv
     *
     * @param string $nomFichierCsv
     *
     * @return Motu
     */
    public function setNomFichierCsv($nomFichierCsv)
    {
        $this->nomFichierCsv = $nomFichierCsv;

        return $this;
    }

    /**
     * Get nomFichierCsv
     *
     * @return string
     */
    public function getNomFichierCsv()
    {
        return $this->nomFichierCsv;
    }

    /**
     * Set dateMotu
     *
     * @param \DateTime $dateMotu
     *
     * @return Motu
     */
    public function setDateMotu($dateMotu)
    {
        $this->dateMotu = $dateMotu;

        return $this;
    }

    /**
     * Get dateMotu
     *
     * @return \DateTime
     */
    public function getDateMotu()
    {
        return $this->dateMotu;
    }

    /**
     * Set commentaireMotu
     *
     * @param string $commentaireMotu
     *
     * @return Motu
     */
    public function setCommentaireMotu($commentaireMotu)
    {
        $this->commentaireMotu = $commentaireMotu;

        return $this;
    }

    /**
     * Get commentaireMotu
     *
     * @return string
     */
    public function getCommentaireMotu()
    {
        return $this->commentaireMotu;
    }

    /**
     * Set dateCre
     *
     * @param \DateTime $dateCre
     *
     * @return Motu
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
     * @return Motu
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
     * @return Motu
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
     * @return Motu
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
     * Add motuEstGenerePar
     *
     * @param \Bbees\E3sBundle\Entity\MotuEstGenerePar $motuEstGenerePar
     *
     * @return Motu
     */
    public function addMotuEstGenerePar(\Bbees\E3sBundle\Entity\MotuEstGenerePar $motuEstGenerePar)
    {
        $motuEstGenerePar->setMotuFk($this);
        $this->motuEstGenerePars[] = $motuEstGenerePar;

        return $this;
    }

    /**
     * Remove motuEstGenerePar
     *
     * @param \Bbees\E3sBundle\Entity\MotuEstGenerePar $motuEstGenerePar
     */
    public function removeMotuEstGenerePar(\Bbees\E3sBundle\Entity\MotuEstGenerePar $motuEstGenerePar)
    {
        $this->motuEstGenerePars->removeElement($motuEstGenerePar);
    }

    /**
     * Get motuEstGenerePars
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMotuEstGenerePars()
    {
        return $this->motuEstGenerePars;
    }

    /**
     * Set libelleMotu
     *
     * @param string $libelleMotu
     *
     * @return Motu
     */
    public function setLibelleMotu($libelleMotu)
    {
        $this->libelleMotu = $libelleMotu;

        return $this;
    }

    /**
     * Get libelleMotu
     *
     * @return string
     */
    public function getLibelleMotu()
    {
        return $this->libelleMotu;
    }
}
