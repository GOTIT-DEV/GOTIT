<?php

namespace Bbees\E3sBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EstEffectuePar
 *
 * @ORM\Table(name="est_effectue_par", indexes={@ORM\Index(name="IDX_EE2A88C9B53CD04C", columns={"personne_fk"}), @ORM\Index(name="IDX_EE2A88C9662D9B98", columns={"collecte_fk"})})
 * @ORM\Entity
 */
class EstEffectuePar
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="est_effectue_par_id_seq", allocationSize=1, initialValue=1)
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
     * @var \Collecte
     *
     * @ORM\ManyToOne(targetEntity="Collecte", inversedBy="estEffectuePars")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="collecte_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $collecteFk;



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
     * @return EstEffectuePar
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
     * @return EstEffectuePar
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
     * @return EstEffectuePar
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
     * @return EstEffectuePar
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
     * @return EstEffectuePar
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
     * Set collecteFk
     *
     * @param \Bbees\E3sBundle\Entity\Collecte $collecteFk
     *
     * @return EstEffectuePar
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
}
