<?php

namespace Bbees\E3sBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LotMaterielExtEstReferenceDans
 *
 * @ORM\Table(name="lot_materiel_ext_est_reference_dans", indexes={@ORM\Index(name="IDX_D2338BB240D80ECD", columns={"lot_materiel_ext_fk"}), @ORM\Index(name="IDX_D2338BB2821B1D3F", columns={"source_fk"})})
 * @ORM\Entity
 */
class LotMaterielExtEstReferenceDans
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="lot_materiel_ext_est_reference_dans_id_seq", allocationSize=1, initialValue=1)
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
     * @var \LotMaterielExt
     *
     * @ORM\ManyToOne(targetEntity="LotMaterielExt", inversedBy="lotMaterielExtEstReferenceDanss")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lot_materiel_ext_fk", referencedColumnName="id")
     * })
     */
    private $lotMaterielExtFk;

    /**
     * @var \Source
     *
     * @ORM\ManyToOne(targetEntity="Source")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="source_fk", referencedColumnName="id")
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
     * @return LotMaterielExtEstReferenceDans
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
     * @return LotMaterielExtEstReferenceDans
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
     * @return LotMaterielExtEstReferenceDans
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
     * @return LotMaterielExtEstReferenceDans
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
     * Set lotMaterielExtFk
     *
     * @param \Bbees\E3sBundle\Entity\LotMaterielExt $lotMaterielExtFk
     *
     * @return LotMaterielExtEstReferenceDans
     */
    public function setLotMaterielExtFk(\Bbees\E3sBundle\Entity\LotMaterielExt $lotMaterielExtFk = null)
    {
        $this->lotMaterielExtFk = $lotMaterielExtFk;

        return $this;
    }

    /**
     * Get lotMaterielExtFk
     *
     * @return \Bbees\E3sBundle\Entity\LotMaterielExt
     */
    public function getLotMaterielExtFk()
    {
        return $this->lotMaterielExtFk;
    }

    /**
     * Set sourceFk
     *
     * @param \Bbees\E3sBundle\Entity\Source $sourceFk
     *
     * @return LotMaterielExtEstReferenceDans
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