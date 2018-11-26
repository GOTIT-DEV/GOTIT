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
 * Adn
 *
 * @ORM\Table(name="adn", uniqueConstraints={@ORM\UniqueConstraint(name="cu_adn_cle_primaire", columns={"code_adn"})}, indexes={@ORM\Index(name="adn_code_adn", columns={"code_adn"}), @ORM\Index(name="cle_etrangere1", columns={"date_precision_voc_fk"}), @ORM\Index(name="cle_etrangere3", columns={"individu_fk"}), @ORM\Index(name="cle_etrangere", columns={"methode_extraction_adn_voc_fk"}), @ORM\Index(name="cle_etrangere2", columns={"boite_fk"}), @ORM\Index(name="IDX_1DCF9AF9C53B46B", columns={"qualite_adn_voc_fk"})})
 * @ORM\Entity
 */
class Adn
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="adn_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code_adn", type="string", length=255, nullable=false)
     */
    private $codeAdn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_adn", type="date", nullable=true)
     */
    private $dateAdn;

    /**
     * @var float
     *
     * @ORM\Column(name="concentration_ng_microlitre", type="float", precision=10, scale=0, nullable=true)
     */
    private $concentrationNgMicrolitre;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire_adn", type="text", nullable=true)
     */
    private $commentaireAdn;

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
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="methode_extraction_adn_voc_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $methodeExtractionAdnVocFk;

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
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="qualite_adn_voc_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $qualiteAdnVocFk;

    /**
     * @var \Boite
     *
     * @ORM\ManyToOne(targetEntity="Boite", inversedBy="adns")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="boite_fk", referencedColumnName="id", nullable=true)
     * })
     */
    private $boiteFk;

    /**
     * @ORM\OneToMany(targetEntity="AdnEstRealisePar", mappedBy="adnFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $adnEstRealisePars;
    
    
    
    public function __construct()
    {
        $this->adnEstRealisePars = new ArrayCollection();
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
     * Set codeAdn
     *
     * @param string $codeAdn
     *
     * @return Adn
     */
    public function setCodeAdn($codeAdn)
    {
        $this->codeAdn = $codeAdn;

        return $this;
    }

    /**
     * Get codeAdn
     *
     * @return string
     */
    public function getCodeAdn()
    {
        return $this->codeAdn;
    }

    /**
     * Set dateAdn
     *
     * @param \DateTime $dateAdn
     *
     * @return Adn
     */
    public function setDateAdn($dateAdn)
    {
        $this->dateAdn = $dateAdn;

        return $this;
    }

    /**
     * Get dateAdn
     *
     * @return \DateTime
     */
    public function getDateAdn()
    {
        return $this->dateAdn;
    }

    /**
     * Set concentrationNgMicrolitre
     *
     * @param float $concentrationNgMicrolitre
     *
     * @return Adn
     */
    public function setConcentrationNgMicrolitre($concentrationNgMicrolitre)
    {
        $this->concentrationNgMicrolitre = $concentrationNgMicrolitre;

        return $this;
    }

    /**
     * Get concentrationNgMicrolitre
     *
     * @return float
     */
    public function getConcentrationNgMicrolitre()
    {
        return $this->concentrationNgMicrolitre;
    }

    /**
     * Set commentaireAdn
     *
     * @param string $commentaireAdn
     *
     * @return Adn
     */
    public function setCommentaireAdn($commentaireAdn)
    {
        $this->commentaireAdn = $commentaireAdn;

        return $this;
    }

    /**
     * Get commentaireAdn
     *
     * @return string
     */
    public function getCommentaireAdn()
    {
        return $this->commentaireAdn;
    }

    /**
     * Set dateCre
     *
     * @param \DateTime $dateCre
     *
     * @return Adn
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
     * @return Adn
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
     * @return Adn
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
     * @return Adn
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
     * @return Adn
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
     * Set methodeExtractionAdnVocFk
     *
     * @param \Bbees\E3sBundle\Entity\Voc $methodeExtractionAdnVocFk
     *
     * @return Adn
     */
    public function setMethodeExtractionAdnVocFk(\Bbees\E3sBundle\Entity\Voc $methodeExtractionAdnVocFk = null)
    {
        $this->methodeExtractionAdnVocFk = $methodeExtractionAdnVocFk;

        return $this;
    }

    /**
     * Get methodeExtractionAdnVocFk
     *
     * @return \Bbees\E3sBundle\Entity\Voc
     */
    public function getMethodeExtractionAdnVocFk()
    {
        return $this->methodeExtractionAdnVocFk;
    }

    /**
     * Set individuFk
     *
     * @param \Bbees\E3sBundle\Entity\Individu $individuFk
     *
     * @return Adn
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
     * Set qualiteAdnVocFk
     *
     * @param \Bbees\E3sBundle\Entity\Voc $qualiteAdnVocFk
     *
     * @return Adn
     */
    public function setQualiteAdnVocFk(\Bbees\E3sBundle\Entity\Voc $qualiteAdnVocFk = null)
    {
        $this->qualiteAdnVocFk = $qualiteAdnVocFk;

        return $this;
    }

    /**
     * Get qualiteAdnVocFk
     *
     * @return \Bbees\E3sBundle\Entity\Voc
     */
    public function getQualiteAdnVocFk()
    {
        return $this->qualiteAdnVocFk;
    }

    /**
     * Set boiteFk
     *
     * @param \Bbees\E3sBundle\Entity\Boite $boiteFk
     *
     * @return Adn
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
     * Add adnEstRealisePar
     *
     * @param \Bbees\E3sBundle\Entity\AdnEstRealisePar $adnEstRealisePar
     *
     * @return Adn
     */
    public function addAdnEstRealisePar(\Bbees\E3sBundle\Entity\AdnEstRealisePar $adnEstRealisePar)
    {
        $adnEstRealisePar->setAdnFk($this);
        $this->adnEstRealisePars[] = $adnEstRealisePar;

        return $this;
    }

    /**
     * Remove adnEstRealisePar
     *
     * @param \Bbees\E3sBundle\Entity\AdnEstRealisePar $adnEstRealisePar
     */
    public function removeAdnEstRealisePar(\Bbees\E3sBundle\Entity\AdnEstRealisePar $adnEstRealisePar)
    {
        $this->adnEstRealisePars->removeElement($adnEstRealisePar);
    }

    /**
     * Get adnEstRealisePars
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdnEstRealisePars()
    {
        return $this->adnEstRealisePars;
    }
}
