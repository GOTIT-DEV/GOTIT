<?php

namespace Bbees\E3sBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SqcEstPublieDans
 *
 * @ORM\Table(name="sqc_est_publie_dans", indexes={@ORM\Index(name="IDX_BA97B9C4821B1D3F", columns={"source_fk"}), @ORM\Index(name="IDX_BA97B9C45BE90E48", columns={"sequence_assemblee_fk"})})
 * @ORM\Entity
 */
class SqcEstPublieDans
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="sqc_est_publie_dans_id_seq", allocationSize=1, initialValue=1)
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
     * @var \Source
     *
     * @ORM\ManyToOne(targetEntity="Source")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="source_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $sourceFk;

    /**
     * @var \SequenceAssemblee
     *
     * @ORM\ManyToOne(targetEntity="SequenceAssemblee", inversedBy="sqcEstPublieDanss")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sequence_assemblee_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $sequenceAssembleeFk;



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
     * @return SqcEstPublieDans
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
     * @return SqcEstPublieDans
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
     * @return SqcEstPublieDans
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
     * @return SqcEstPublieDans
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
     * Set sourceFk
     *
     * @param \Bbees\E3sBundle\Entity\Source $sourceFk
     *
     * @return SqcEstPublieDans
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

    /**
     * Set sequenceAssembleeFk
     *
     * @param \Bbees\E3sBundle\Entity\SequenceAssemblee $sequenceAssembleeFk
     *
     * @return SqcEstPublieDans
     */
    public function setSequenceAssembleeFk(\Bbees\E3sBundle\Entity\SequenceAssemblee $sequenceAssembleeFk = null)
    {
        $this->sequenceAssembleeFk = $sequenceAssembleeFk;

        return $this;
    }

    /**
     * Get sequenceAssembleeFk
     *
     * @return \Bbees\E3sBundle\Entity\SequenceAssemblee
     */
    public function getSequenceAssembleeFk()
    {
        return $this->sequenceAssembleeFk;
    }
}
