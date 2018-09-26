<?php

namespace Bbees\E3sBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IndividuLameEstRealisePar
 *
 * @ORM\Table(name="individu_lame_est_realise_par", indexes={@ORM\Index(name="IDX_88295540D9C85992", columns={"individu_lame_fk"}), @ORM\Index(name="IDX_88295540B53CD04C", columns={"personne_fk"})})
 * @ORM\Entity
 */
class IndividuLameEstRealisePar
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="individu_lame_est_realise_par_id_seq", allocationSize=1, initialValue=1)
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
     * @var \IndividuLame
     *
     * @ORM\ManyToOne(targetEntity="IndividuLame", inversedBy="individuLameEstRealisePars")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="individu_lame_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $individuLameFk;

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
     * @return IndividuLameEstRealisePar
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
     * @return IndividuLameEstRealisePar
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
     * @return IndividuLameEstRealisePar
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
     * @return IndividuLameEstRealisePar
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
     * Set individuLameFk
     *
     * @param \Bbees\E3sBundle\Entity\IndividuLame $individuLameFk
     *
     * @return IndividuLameEstRealisePar
     */
    public function setIndividuLameFk(\Bbees\E3sBundle\Entity\IndividuLame $individuLameFk = null)
    {
        $this->individuLameFk = $individuLameFk;

        return $this;
    }

    /**
     * Get individuLameFk
     *
     * @return \Bbees\E3sBundle\Entity\IndividuLame
     */
    public function getIndividuLameFk()
    {
        return $this->individuLameFk;
    }

    /**
     * Set personneFk
     *
     * @param \Bbees\E3sBundle\Entity\Personne $personneFk
     *
     * @return IndividuLameEstRealisePar
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
}
