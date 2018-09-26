<?php

namespace Bbees\E3sBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EstFinancePar
 *
 * @ORM\Table(name="est_finance_par", indexes={@ORM\Index(name="IDX_18FCBB8F759C7BB0", columns={"programme_fk"}), @ORM\Index(name="IDX_18FCBB8F662D9B98", columns={"collecte_fk"})})
 * @ORM\Entity
 */
class EstFinancePar
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="est_finance_par_id_seq", allocationSize=1, initialValue=1)
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
     * @var \Programme
     *
     * @ORM\ManyToOne(targetEntity="Programme")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="programme_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $programmeFk;

    /**
     * @var \Collecte
     *
     * @ORM\ManyToOne(targetEntity="Collecte", inversedBy="estFinancePars")
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
     * @return EstFinancePar
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
     * @return EstFinancePar
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
     * @return EstFinancePar
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
     * @return EstFinancePar
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
     * Set programmeFk
     *
     * @param \Bbees\E3sBundle\Entity\Programme $programmeFk
     *
     * @return EstFinancePar
     */
    public function setProgrammeFk(\Bbees\E3sBundle\Entity\Programme $programmeFk = null)
    {
        $this->programmeFk = $programmeFk;

        return $this;
    }

    /**
     * Get programmeFk
     *
     * @return \Bbees\E3sBundle\Entity\Programme
     */
    public function getProgrammeFk()
    {
        return $this->programmeFk;
    }

    /**
     * Set collecteFk
     *
     * @param \Bbees\E3sBundle\Entity\Collecte $collecteFk
     *
     * @return EstFinancePar
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
