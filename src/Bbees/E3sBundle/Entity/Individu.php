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
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Individu
 *
 * @ORM\Table(name="individu", uniqueConstraints={@ORM\UniqueConstraint(name="cu_individu_code_ind_tri_morpho", columns={"code_ind_tri_morpho"}), @ORM\UniqueConstraint(name="cu_individu_code_ind_biomol", columns={"code_ind_biomol"})}, indexes={@ORM\Index(name="IDX_5EE42FCE4236D33E", columns={"type_individu_voc_fk"}), @ORM\Index(name="IDX_5EE42FCE54DBBD4D", columns={"lot_materiel_fk"})})
 * @ORM\Entity
 */
class Individu
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="individu_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code_ind_biomol", type="string", length=255, nullable=true)
     */
    private $codeIndBiomol;

    /**
     * @var string
     *
     * @ORM\Column(name="code_ind_tri_morpho", type="string", length=255, nullable=false)
     */
    private $codeIndTriMorpho;

    /**
     * @var string
     *
     * @ORM\Column(name="code_tube", type="string", length=255, nullable=false)
     */
    private $codeTube;

    /**
     * @var string
     *
     * @ORM\Column(name="num_ind_biomol", type="string", length=255, nullable=true)
     */
    private $numIndBiomol;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire_ind", type="text", nullable=true)
     */
    private $commentaireInd;

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
     *   @ORM\JoinColumn(name="type_individu_voc_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $typeIndividuVocFk;

    /**
     * @var \LotMateriel
     *
     * @ORM\ManyToOne(targetEntity="LotMateriel")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lot_materiel_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $lotMaterielFk;
    
    /**
     * @ORM\OneToMany(targetEntity="EspeceIdentifiee", mappedBy="individuFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $especeIdentifiees;
    
    
    public function __construct()
    {
        $this->especeIdentifiees = new ArrayCollection();
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
     * Set codeIndBiomol
     *
     * @param string $codeIndBiomol
     *
     * @return Individu
     */
    public function setCodeIndBiomol($codeIndBiomol)
    {
        $this->codeIndBiomol = $codeIndBiomol;

        return $this;
    }

    /**
     * Get codeIndBiomol
     *
     * @return string
     */
    public function getCodeIndBiomol()
    {
        return $this->codeIndBiomol;
    }

    /**
     * Set codeIndTriMorpho
     *
     * @param string $codeIndTriMorpho
     *
     * @return Individu
     */
    public function setCodeIndTriMorpho($codeIndTriMorpho)
    {
        $this->codeIndTriMorpho = $codeIndTriMorpho;

        return $this;
    }

    /**
     * Get codeIndTriMorpho
     *
     * @return string
     */
    public function getCodeIndTriMorpho()
    {
        return $this->codeIndTriMorpho;
    }

    /**
     * Set codeTube
     *
     * @param string $codeTube
     *
     * @return Individu
     */
    public function setCodeTube($codeTube)
    {
        $this->codeTube = $codeTube;

        return $this;
    }

    /**
     * Get codeTube
     *
     * @return string
     */
    public function getCodeTube()
    {
        return $this->codeTube;
    }

    /**
     * Set numIndBiomol
     *
     * @param string $numIndBiomol
     *
     * @return Individu
     */
    public function setNumIndBiomol($numIndBiomol)
    {
        $this->numIndBiomol = $numIndBiomol;

        return $this;
    }

    /**
     * Get numIndBiomol
     *
     * @return string
     */
    public function getNumIndBiomol()
    {
        return $this->numIndBiomol;
    }

    /**
     * Set commentaireInd
     *
     * @param string $commentaireInd
     *
     * @return Individu
     */
    public function setCommentaireInd($commentaireInd)
    {
        $this->commentaireInd = $commentaireInd;

        return $this;
    }

    /**
     * Get commentaireInd
     *
     * @return string
     */
    public function getCommentaireInd()
    {
        return $this->commentaireInd;
    }

    /**
     * Set dateCre
     *
     * @param \DateTime $dateCre
     *
     * @return Individu
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
     * @return Individu
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
     * @return Individu
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
     * @return Individu
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
     * Set typeIndividuVocFk
     *
     * @param \Bbees\E3sBundle\Entity\Voc $typeIndividuVocFk
     *
     * @return Individu
     */
    public function setTypeIndividuVocFk(\Bbees\E3sBundle\Entity\Voc $typeIndividuVocFk = null)
    {
        $this->typeIndividuVocFk = $typeIndividuVocFk;

        return $this;
    }

    /**
     * Get typeIndividuVocFk
     *
     * @return \Bbees\E3sBundle\Entity\Voc
     */
    public function getTypeIndividuVocFk()
    {
        return $this->typeIndividuVocFk;
    }

    /**
     * Set lotMaterielFk
     *
     * @param \Bbees\E3sBundle\Entity\LotMateriel $lotMaterielFk
     *
     * @return Individu
     */
    public function setLotMaterielFk(\Bbees\E3sBundle\Entity\LotMateriel $lotMaterielFk = null)
    {
        $this->lotMaterielFk = $lotMaterielFk;

        return $this;
    }

    /**
     * Get lotMaterielFk
     *
     * @return \Bbees\E3sBundle\Entity\LotMateriel
     */
    public function getLotMaterielFk()
    {
        return $this->lotMaterielFk;
    }

    /**
     * Add especeIdentifiee
     *
     * @param \Bbees\E3sBundle\Entity\EspeceIdentifiee $especeIdentifiee
     *
     * @return Individu
     */
    public function addEspeceIdentifiee(\Bbees\E3sBundle\Entity\EspeceIdentifiee $especeIdentifiee)
    {
        $especeIdentifiee->setIndividuFk($this);
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
}
