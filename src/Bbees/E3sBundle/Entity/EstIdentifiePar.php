<?php

namespace Bbees\E3sBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EstIdentifiePar
 *
 * @ORM\Table(name="est_identifie_par", indexes={@ORM\Index(name="IDX_F8FCCF63B53CD04C", columns={"personne_fk"}), @ORM\Index(name="IDX_F8FCCF63B4AB6BA0", columns={"espece_identifiee_fk"})})
 * @ORM\Entity
 */
class EstIdentifiePar
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="est_identifie_par_id_seq", allocationSize=1, initialValue=1)
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
     * @var \EspeceIdentifiee
     *
     * @ORM\ManyToOne(targetEntity="EspeceIdentifiee", inversedBy="estIdentifiePars")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="espece_identifiee_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $especeIdentifieeFk;



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
     * @return EstIdentifiePar
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
     * @return EstIdentifiePar
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
     * @return EstIdentifiePar
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
     * @return EstIdentifiePar
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
     * @return EstIdentifiePar
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
     * Set especeIdentifieeFk
     *
     * @param \Bbees\E3sBundle\Entity\EspeceIdentifiee $especeIdentifieeFk
     *
     * @return EstIdentifiePar
     */
    public function setEspeceIdentifieeFk(\Bbees\E3sBundle\Entity\EspeceIdentifiee $especeIdentifieeFk = null)
    {
        $this->especeIdentifieeFk = $especeIdentifieeFk;

        return $this;
    }

    /**
     * Get especeIdentifieeFk
     *
     * @return \Bbees\E3sBundle\Entity\EspeceIdentifiee
     */
    public function getEspeceIdentifieeFk()
    {
        return $this->especeIdentifieeFk;
    }
}
