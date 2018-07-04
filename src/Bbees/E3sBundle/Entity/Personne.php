<?php

namespace Bbees\E3sBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Personne
 *
 * @ORM\Table(name="personne", uniqueConstraints={@ORM\UniqueConstraint(name="cu_personne_cle_primaire", columns={"nom_personne"})}, indexes={@ORM\Index(name="IDX_FCEC9EFE8441376", columns={"etablissement_fk"})})
 * @ORM\Entity
 */
class Personne
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="personne_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_personne", type="string", length=255, nullable=false)
     */
    private $nomPersonne;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_complet", type="string", length=1024, nullable=true)
     */
    private $nomComplet;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_personne_ref", type="string", length=255, nullable=true)
     */
    private $nomPersonneRef;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire_personne", type="text", nullable=true)
     */
    private $commentairePersonne;

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
     * @var \Etablissement
     *
     * @ORM\ManyToOne(targetEntity="Etablissement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="etablissement_fk", referencedColumnName="id")
     * })
     */
    private $etablissementFk;



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
     * Set nomPersonne
     *
     * @param string $nomPersonne
     *
     * @return Personne
     */
    public function setNomPersonne($nomPersonne)
    {
        $this->nomPersonne = $nomPersonne;

        return $this;
    }

    /**
     * Get nomPersonne
     *
     * @return string
     */
    public function getNomPersonne()
    {
        return $this->nomPersonne;
    }

    /**
     * Set nomComplet
     *
     * @param string $nomComplet
     *
     * @return Personne
     */
    public function setNomComplet($nomComplet)
    {
        $this->nomComplet = $nomComplet;

        return $this;
    }

    /**
     * Get nomComplet
     *
     * @return string
     */
    public function getNomComplet()
    {
        return $this->nomComplet;
    }

    /**
     * Set nomPersonneRef
     *
     * @param string $nomPersonneRef
     *
     * @return Personne
     */
    public function setNomPersonneRef($nomPersonneRef)
    {
        $this->nomPersonneRef = $nomPersonneRef;

        return $this;
    }

    /**
     * Get nomPersonneRef
     *
     * @return string
     */
    public function getNomPersonneRef()
    {
        return $this->nomPersonneRef;
    }

    /**
     * Set commentairePersonne
     *
     * @param string $commentairePersonne
     *
     * @return Personne
     */
    public function setCommentairePersonne($commentairePersonne)
    {
        $this->commentairePersonne = $commentairePersonne;

        return $this;
    }

    /**
     * Get commentairePersonne
     *
     * @return string
     */
    public function getCommentairePersonne()
    {
        return $this->commentairePersonne;
    }

    /**
     * Set dateCre
     *
     * @param \DateTime $dateCre
     *
     * @return Personne
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
     * @return Personne
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
     * @return Personne
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
     * @return Personne
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
     * Set etablissementFk
     *
     * @param \Bbees\E3sBundle\Entity\Etablissement $etablissementFk
     *
     * @return Personne
     */
    public function setEtablissementFk(\Bbees\E3sBundle\Entity\Etablissement $etablissementFk = null)
    {
        $this->etablissementFk = $etablissementFk;

        return $this;
    }

    /**
     * Get etablissementFk
     *
     * @return \Bbees\E3sBundle\Entity\Etablissement
     */
    public function getEtablissementFk()
    {
        return $this->etablissementFk;
    }
}
