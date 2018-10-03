<?php

namespace Bbees\E3sBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LotEstPublieDans
 *
 * @ORM\Table(name="lot_est_publie_dans", indexes={@ORM\Index(name="IDX_EA07BFA754DBBD4D", columns={"lot_materiel_fk"}), @ORM\Index(name="IDX_EA07BFA7821B1D3F", columns={"source_fk"})})
 * @ORM\Entity
 */
class LotEstPublieDans
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="lot_est_publie_dans_id_seq", allocationSize=1, initialValue=1)
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
     * @var \LotMateriel
     *
     * @ORM\ManyToOne(targetEntity="LotMateriel", inversedBy="lotEstPublieDanss")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lot_materiel_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $lotMaterielFk;

    /**
     * @var \Source
     *
     * @ORM\ManyToOne(targetEntity="Source")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="source_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $sourceFk;



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
     * @return LotEstPublieDans
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
     * @return LotEstPublieDans
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
     * @return LotEstPublieDans
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
     * @return LotEstPublieDans
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
     * Set lotMaterielFk
     *
     * @param \Bbees\E3sBundle\Entity\LotMateriel $lotMaterielFk
     *
     * @return LotEstPublieDans
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
     * Set sourceFk
     *
     * @param \Bbees\E3sBundle\Entity\Source $sourceFk
     *
     * @return LotEstPublieDans
     */
    public function setSourceFk(\Bbees\E3sBundle\Entity\Source $sourceFk = null)
    {
        $this->sourceFk = $sourceFk;

        return $this;
    }

    /**
     * Get sourceFk
     *
     * @return \Bbees\E3sBundle\Entity\Source
     */
    public function getSourceFk()
    {
        return $this->sourceFk;
    }
}
