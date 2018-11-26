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
 * IndividuLame
 *
 * @ORM\Table(name="individu_lame", uniqueConstraints={@ORM\UniqueConstraint(name="cu_individu_lame_cle_primaire", columns={"code_lame_coll"})}, indexes={@ORM\Index(name="IDX_8DA827E2A30C442F", columns={"date_precision_voc_fk"}), @ORM\Index(name="IDX_8DA827E22B644673", columns={"boite_fk"}), @ORM\Index(name="IDX_8DA827E25F2C6176", columns={"individu_fk"})})
 * @ORM\Entity
 */
class IndividuLame
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="individu_lame_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code_lame_coll", type="string", length=255, nullable=false)
     */
    private $codeLameColl;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle_lame", type="string", length=1024, nullable=false)
     */
    private $libelleLame;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_lame", type="date", nullable=true)
     */
    private $dateLame;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_dossier_photos", type="string", length=1024, nullable=true)
     */
    private $nomDossierPhotos;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire_lame", type="text", nullable=true)
     */
    private $commentaireLame;

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
     * @var \Boite
     *
     * @ORM\ManyToOne(targetEntity="Boite", inversedBy="individuLames")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="boite_fk", referencedColumnName="id", nullable=true)
     * })
     */
    private $boiteFk;

    /**
     * @var \Individu
     *
     * @ORM\ManyToOne(targetEntity="Individu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="individu_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $individuFk;

    /**
     * @ORM\OneToMany(targetEntity="IndividuLameEstRealisePar", mappedBy="individuLameFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $individuLameEstRealisePars;
    
    
    
    public function __construct()
    {
        $this->individuLameEstRealisePars = new ArrayCollection();
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
     * Set codeLameColl
     *
     * @param string $codeLameColl
     *
     * @return IndividuLame
     */
    public function setCodeLameColl($codeLameColl)
    {
        $this->codeLameColl = $codeLameColl;

        return $this;
    }

    /**
     * Get codeLameColl
     *
     * @return string
     */
    public function getCodeLameColl()
    {
        return $this->codeLameColl;
    }

    /**
     * Set libelleLame
     *
     * @param string $libelleLame
     *
     * @return IndividuLame
     */
    public function setLibelleLame($libelleLame)
    {
        $this->libelleLame = $libelleLame;

        return $this;
    }

    /**
     * Get libelleLame
     *
     * @return string
     */
    public function getLibelleLame()
    {
        return $this->libelleLame;
    }

    /**
     * Set dateLame
     *
     * @param \DateTime $dateLame
     *
     * @return IndividuLame
     */
    public function setDateLame($dateLame)
    {
        $this->dateLame = $dateLame;

        return $this;
    }

    /**
     * Get dateLame
     *
     * @return \DateTime
     */
    public function getDateLame()
    {
        return $this->dateLame;
    }

    /**
     * Set nomDossierPhotos
     *
     * @param string $nomDossierPhotos
     *
     * @return IndividuLame
     */
    public function setNomDossierPhotos($nomDossierPhotos)
    {
        $this->nomDossierPhotos = $nomDossierPhotos;

        return $this;
    }

    /**
     * Get nomDossierPhotos
     *
     * @return string
     */
    public function getNomDossierPhotos()
    {
        return $this->nomDossierPhotos;
    }

    /**
     * Set commentaireLame
     *
     * @param string $commentaireLame
     *
     * @return IndividuLame
     */
    public function setCommentaireLame($commentaireLame)
    {
        $this->commentaireLame = $commentaireLame;

        return $this;
    }

    /**
     * Get commentaireLame
     *
     * @return string
     */
    public function getCommentaireLame()
    {
        return $this->commentaireLame;
    }

    /**
     * Set dateCre
     *
     * @param \DateTime $dateCre
     *
     * @return IndividuLame
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
     * @return IndividuLame
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
     * @return IndividuLame
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
     * @return IndividuLame
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
     * @return IndividuLame
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
     * Set boiteFk
     *
     * @param \Bbees\E3sBundle\Entity\Boite $boiteFk
     *
     * @return IndividuLame
     */
    public function setBoiteFk(\Bbees\E3sBundle\Entity\Boite $boiteFk = null)
    {
        $this->boiteFk = $boiteFk;

        return $this;
    }

    /**
     * Get boiteFk
     *
     * @return \Bbees\E3sBundle\Entity\Boite
     */
    public function getBoiteFk()
    {
        return $this->boiteFk;
    }

    /**
     * Set individuFk
     *
     * @param \Bbees\E3sBundle\Entity\Individu $individuFk
     *
     * @return IndividuLame
     */
    public function setIndividuFk(\Bbees\E3sBundle\Entity\Individu $individuFk = null)
    {
        $this->individuFk = $individuFk;

        return $this;
    }

    /**
     * Get individuFk
     *
     * @return \Bbees\E3sBundle\Entity\Individu
     */
    public function getIndividuFk()
    {
        return $this->individuFk;
    }

    /**
     * Add individuLameEstRealisePar
     *
     * @param \Bbees\E3sBundle\Entity\IndividuLameEstRealisePar $individuLameEstRealisePar
     *
     * @return IndividuLame
     */
    public function addIndividuLameEstRealisePar(\Bbees\E3sBundle\Entity\IndividuLameEstRealisePar $individuLameEstRealisePar)
    {
        $individuLameEstRealisePar->setIndividuLameFk($this);
        $this->individuLameEstRealisePars[] = $individuLameEstRealisePar;

        return $this;
    }

    /**
     * Remove individuLameEstRealisePar
     *
     * @param \Bbees\E3sBundle\Entity\IndividuLameEstRealisePar $individuLameEstRealisePar
     */
    public function removeIndividuLameEstRealisePar(\Bbees\E3sBundle\Entity\IndividuLameEstRealisePar $individuLameEstRealisePar)
    {
        $this->individuLameEstRealisePars->removeElement($individuLameEstRealisePar);
    }

    /**
     * Get individuLameEstRealisePars
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIndividuLameEstRealisePars()
    {
        return $this->individuLameEstRealisePars;
    }
}
