<?php

namespace Bbees\E3sBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Assigne
 *
 * @ORM\Table(name="assigne", indexes={@ORM\Index(name="IDX_4E79CB8DCDD1F756", columns={"sequence_assemblee_ext_fk"}), @ORM\Index(name="IDX_4E79CB8D40E7E0B3", columns={"methode_motu_voc_fk"}), @ORM\Index(name="IDX_4E79CB8D5BE90E48", columns={"sequence_assemblee_fk"}), @ORM\Index(name="IDX_4E79CB8D503B4409", columns={"motu_fk"})})
 * @ORM\Entity
 */
class Assigne
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="assigne_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_motu", type="bigint", nullable=false)
     */
    private $numMotu;

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
     * @var \SequenceAssembleeExt
     *
     * @ORM\ManyToOne(targetEntity="SequenceAssembleeExt")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sequence_assemblee_ext_fk", referencedColumnName="id")
     * })
     */
    private $sequenceAssembleeExtFk;

    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="methode_motu_voc_fk", referencedColumnName="id")
     * })
     */
    private $methodeMotuVocFk;

    /**
     * @var \SequenceAssemblee
     *
     * @ORM\ManyToOne(targetEntity="SequenceAssemblee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sequence_assemblee_fk", referencedColumnName="id")
     * })
     */
    private $sequenceAssembleeFk;

    /**
     * @var \Motu
     *
     * @ORM\ManyToOne(targetEntity="Motu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="motu_fk", referencedColumnName="id")
     * })
     */
    private $motuFk;



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
     * Set numMotu
     *
     * @param integer $numMotu
     *
     * @return Assigne
     */
    public function setNumMotu($numMotu)
    {
        $this->numMotu = $numMotu;

        return $this;
    }

    /**
     * Get numMotu
     *
     * @return integer
     */
    public function getNumMotu()
    {
        return $this->numMotu;
    }

    /**
     * Set dateCre
     *
     * @param \DateTime $dateCre
     *
     * @return Assigne
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
     * @return Assigne
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
     * @return Assigne
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
     * @return Assigne
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
     * Set sequenceAssembleeExtFk
     *
     * @param \Bbees\E3sBundle\Entity\SequenceAssembleeExt $sequenceAssembleeExtFk
     *
     * @return Assigne
     */
    public function setSequenceAssembleeExtFk(\Bbees\E3sBundle\Entity\SequenceAssembleeExt $sequenceAssembleeExtFk = null)
    {
        $this->sequenceAssembleeExtFk = $sequenceAssembleeExtFk;

        return $this;
    }

    /**
     * Get sequenceAssembleeExtFk
     *
     * @return \Bbees\E3sBundle\Entity\SequenceAssembleeExt
     */
    public function getSequenceAssembleeExtFk()
    {
        return $this->sequenceAssembleeExtFk;
    }

    /**
     * Set methodeMotuVocFk
     *
     * @param \Bbees\E3sBundle\Entity\Voc $methodeMotuVocFk
     *
     * @return Assigne
     */
    public function setMethodeMotuVocFk(\Bbees\E3sBundle\Entity\Voc $methodeMotuVocFk = null)
    {
        $this->methodeMotuVocFk = $methodeMotuVocFk;

        return $this;
    }

    /**
     * Get methodeMotuVocFk
     *
     * @return \Bbees\E3sBundle\Entity\Voc
     */
    public function getMethodeMotuVocFk()
    {
        return $this->methodeMotuVocFk;
    }

    /**
     * Set sequenceAssembleeFk
     *
     * @param \Bbees\E3sBundle\Entity\SequenceAssemblee $sequenceAssembleeFk
     *
     * @return Assigne
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

    /**
     * Set motuFk
     *
     * @param \Bbees\E3sBundle\Entity\Motu $motuFk
     *
     * @return Assigne
     */
    public function setMotuFk(\Bbees\E3sBundle\Entity\Motu $motuFk = null)
    {
        $this->motuFk = $motuFk;

        return $this;
    }

    /**
     * Get motuFk
     *
     * @return \Bbees\E3sBundle\Entity\Motu
     */
    public function getMotuFk()
    {
        return $this->motuFk;
    }
}
