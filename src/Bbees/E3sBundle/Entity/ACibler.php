<?php

/*
 * This file is part of the Bundle E3s package.
 * 
 *
 * (c) Philippe Grison <philippe.grison@mnhn.fr>
 * 
 * Unité Bases de données sur la Biodiversité, Écologie, Environnement et Sociétés (BBEES), 
 * Muséum national d'Histoire naturelle, CNRS, Sorbonne Université ; CP55, 57 rue Cuvier 75005 Paris, France
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bbees\E3sBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ACibler
 *
 * @ORM\Table(name="a_cibler", indexes={@ORM\Index(name="IDX_C0DF0CE4662D9B98", columns={"collecte_fk"}), @ORM\Index(name="IDX_C0DF0CE47B09E3BC", columns={"referentiel_taxon_fk"})})
 * @ORM\Entity
 */
class ACibler
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="a_cibler_id_seq", allocationSize=1, initialValue=1)
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
     * @var \Collecte
     *
     * @ORM\ManyToOne(targetEntity="Collecte", inversedBy="aCiblers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="collecte_fk", referencedColumnName="id")
     * })
     */
    private $collecteFk;

    /**
     * @var \ReferentielTaxon
     *
     * @ORM\ManyToOne(targetEntity="ReferentielTaxon")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="referentiel_taxon_fk", referencedColumnName="id")
     * })
     */
    private $referentielTaxonFk;



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
     * @return ACibler
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
     * @return ACibler
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
     * @return ACibler
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
     * @return ACibler
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
     * Set collecteFk
     *
     * @param \Bbees\E3sBundle\Entity\Collecte $collecteFk
     *
     * @return ACibler
     */
    public function setCollecteFk(\Bbees\E3sBundle\Entity\Collecte $collecteFk = null)
    {
        $this->collecteFk = $collecteFk;

        return $this;
    }

    /**
     * Get collecteFk
     *
     * @return \Bbees\E3sBundle\Entity\Collecte
     */
    public function getCollecteFk()
    {
        return $this->collecteFk;
    }

    /**
     * Set referentielTaxonFk
     *
     * @param \Bbees\E3sBundle\Entity\ReferentielTaxon $referentielTaxonFk
     *
     * @return ACibler
     */
    public function setReferentielTaxonFk(\Bbees\E3sBundle\Entity\ReferentielTaxon $referentielTaxonFk = null)
    {
        $this->referentielTaxonFk = $referentielTaxonFk;

        return $this;
    }

    /**
     * Get referentielTaxonFk
     *
     * @return \Bbees\E3sBundle\Entity\ReferentielTaxon
     */
    public function getReferentielTaxonFk()
    {
        return $this->referentielTaxonFk;
    }
}
