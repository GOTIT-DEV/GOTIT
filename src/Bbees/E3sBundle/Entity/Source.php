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
 * Source
 *
 * @ORM\Table(name="source", uniqueConstraints={@ORM\UniqueConstraint(name="cu_source_cle_primaire", columns={"code_source"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Source
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="source_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code_source", type="string", length=255, nullable=false)
     */
    private $codeSource;

    /**
     * @var integer
     *
     * @ORM\Column(name="annee_source", type="bigint", nullable=true)
     */
    private $anneeSource;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle_source", type="string", length=2048, nullable=false)
     */
    private $libelleSource;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire_source", type="text", nullable=true)
     */
    private $commentaireSource;

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
     * @ORM\OneToMany(targetEntity="SourceAEteIntegrePar", mappedBy="sourceFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $sourceAEteIntegrePars;
    
    
    
    public function __construct()
    {
        $this->sourceAEteIntegrePars = new ArrayCollection();
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
     * Set codeSource
     *
     * @param string $codeSource
     *
     * @return Source
     */
    public function setCodeSource($codeSource)
    {
        $this->codeSource = $codeSource;

        return $this;
    }

    /**
     * Get codeSource
     *
     * @return string
     */
    public function getCodeSource()
    {
        return $this->codeSource;
    }

    /**
     * Set anneeSource
     *
     * @param integer $anneeSource
     *
     * @return Source
     */
    public function setAnneeSource($anneeSource)
    {
        $this->anneeSource = $anneeSource;

        return $this;
    }

    /**
     * Get anneeSource
     *
     * @return integer
     */
    public function getAnneeSource()
    {
        return $this->anneeSource;
    }

    /**
     * Set libelleSource
     *
     * @param string $libelleSource
     *
     * @return Source
     */
    public function setLibelleSource($libelleSource)
    {
        $this->libelleSource = $libelleSource;

        return $this;
    }

    /**
     * Get libelleSource
     *
     * @return string
     */
    public function getLibelleSource()
    {
        return $this->libelleSource;
    }

    /**
     * Set commentaireSource
     *
     * @param string $commentaireSource
     *
     * @return Source
     */
    public function setCommentaireSource($commentaireSource)
    {
        $this->commentaireSource = $commentaireSource;

        return $this;
    }

    /**
     * Get commentaireSource
     *
     * @return string
     */
    public function getCommentaireSource()
    {
        return $this->commentaireSource;
    }

    /**
     * Set dateCre
     *
     * @param \DateTime $dateCre
     *
     * @return Source
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
     * @return Source
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
     * @return Source
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
     * @return Source
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
     * Add sourceAEteIntegrePar
     *
     * @param \Bbees\E3sBundle\Entity\SourceAEteIntegrePar $sourceAEteIntegrePar
     *
     * @return Source
     */
    public function addSourceAEteIntegrePar(\Bbees\E3sBundle\Entity\SourceAEteIntegrePar $sourceAEteIntegrePar)
    {
        $sourceAEteIntegrePar->setSourceFk($this);
        $this->sourceAEteIntegrePars[] = $sourceAEteIntegrePar;

        return $this;
    }

    /**
     * Remove sourceAEteIntegrePar
     *
     * @param \Bbees\E3sBundle\Entity\SourceAEteIntegrePar $sourceAEteIntegrePar
     */
    public function removeSourceAEteIntegrePar(\Bbees\E3sBundle\Entity\SourceAEteIntegrePar $sourceAEteIntegrePar)
    {
        $this->sourceAEteIntegrePars->removeElement($sourceAEteIntegrePar);
    }

    /**
     * Get sourceAEteIntegrePars
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSourceAEteIntegrePars()
    {
        return $this->sourceAEteIntegrePars;
    }
}
