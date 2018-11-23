<?php

namespace Bbees\E3sBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * SequenceAssemblee
 *
 * @ORM\Table(name="sequence_assemblee", uniqueConstraints={@ORM\UniqueConstraint(name="cu_sequence_assemblee_cle_primaire", columns={"code_sqc_ass"}), @ORM\UniqueConstraint(name="cu_sequence_assemblee_code_sqc_alignement", columns={"code_sqc_alignement"})}, indexes={@ORM\Index(name="IDX_353CF669A30C442F", columns={"date_precision_voc_fk"}), @ORM\Index(name="IDX_353CF66988085E0F", columns={"statut_sqc_ass_voc_fk"})})
 * @ORM\Entity
 */
class SequenceAssemblee
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="sequence_assemblee_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code_sqc_ass", type="string", length=1024, nullable=false)
     */
    private $codeSqcAss;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation_sqc_ass", type="date", nullable=true)
     */
    private $dateCreationSqcAss;

    /**
     * @var string
     *
     * @ORM\Column(name="accession_number", type="string", length=255, nullable=true)
     */
    private $accessionNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="code_sqc_alignement", type="string", length=1024, nullable=true)
     */
    private $codeSqcAlignement;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire_sqc_ass", type="text", nullable=true)
     */
    private $commentaireSqcAss;

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
     * @var integer
     */
    private $geneVocFk;

    /**
     * @var integer
     */
    private $individuFk;


    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="date_precision_voc_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $datePrecisionVocFk;

    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="statut_sqc_ass_voc_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $statutSqcAssVocFk;

    /**
     * @ORM\OneToMany(targetEntity="SequenceAssembleeEstRealisePar", mappedBy="sequenceAssembleeFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $sequenceAssembleeEstRealisePars;
    
    /**
     * @ORM\OneToMany(targetEntity="SqcEstPublieDans", mappedBy="sequenceAssembleeFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $sqcEstPublieDanss;
  
    /**
     * @ORM\OneToMany(targetEntity="EspeceIdentifiee", mappedBy="sequenceAssembleeFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $especeIdentifiees;
    
    /**
     * @ORM\OneToMany(targetEntity="EstAligneEtTraite", mappedBy="sequenceAssembleeFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $estAligneEtTraites;
    
    
    
    public function __construct()
    {
        $this->sequenceAssembleeEstRealisePars = new ArrayCollection();
    	$this->sqcEstPublieDanss = new ArrayCollection();
        $this->especeIdentifiees = new ArrayCollection();
        $this->estAligneEtTraites = new ArrayCollection();
    }


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
     * Set codeSqcAss
     *
     * @param string $codeSqcAss
     *
     * @return SequenceAssemblee
     */
    public function setCodeSqcAss($codeSqcAss)
    {
        $this->codeSqcAss = $codeSqcAss;

        return $this;
    }

    /**
     * Get codeSqcAss
     *
     * @return string
     */
    public function getCodeSqcAss()
    {
        return $this->codeSqcAss;
    }

    /**
     * Set dateCreationSqcAss
     *
     * @param \DateTime $dateCreationSqcAss
     *
     * @return SequenceAssemblee
     */
    public function setDateCreationSqcAss($dateCreationSqcAss)
    {
        $this->dateCreationSqcAss = $dateCreationSqcAss;

        return $this;
    }

    /**
     * Get dateCreationSqcAss
     *
     * @return \DateTime
     */
    public function getDateCreationSqcAss()
    {
        return $this->dateCreationSqcAss;
    }

    /**
     * Set accessionNumber
     *
     * @param string $accessionNumber
     *
     * @return SequenceAssemblee
     */
    public function setAccessionNumber($accessionNumber)
    {
        $this->accessionNumber = $accessionNumber;

        return $this;
    }

    /**
     * Get accessionNumber
     *
     * @return string
     */
    public function getAccessionNumber()
    {
        return $this->accessionNumber;
    }

    /**
     * Set codeSqcAlignement
     *
     * @param string $codeSqcAlignement
     *
     * @return SequenceAssemblee
     */
    public function setCodeSqcAlignement($codeSqcAlignement)
    {
        $this->codeSqcAlignement = $codeSqcAlignement;

        return $this;
    }

    /**
     * Get codeSqcAlignement
     *
     * @return string
     */
    public function getCodeSqcAlignement()
    {
        return $this->codeSqcAlignement;
    }

    /**
     * Set commentaireSqcAss
     *
     * @param string $commentaireSqcAss
     *
     * @return SequenceAssemblee
     */
    public function setCommentaireSqcAss($commentaireSqcAss)
    {
        $this->commentaireSqcAss = $commentaireSqcAss;

        return $this;
    }

    /**
     * Get commentaireSqcAss
     *
     * @return string
     */
    public function getCommentaireSqcAss()
    {
        return $this->commentaireSqcAss;
    }

    /**
     * Set dateCre
     *
     * @param \DateTime $dateCre
     *
     * @return SequenceAssemblee
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
     * @return SequenceAssemblee
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
     * @return SequenceAssemblee
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
     * @return SequenceAssemblee
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
     * Set datePrecisionVocFk
     *
     * @param \Bbees\E3sBundle\Entity\Voc $datePrecisionVocFk
     *
     * @return SequenceAssemblee
     */
    public function setDatePrecisionVocFk(\Bbees\E3sBundle\Entity\Voc $datePrecisionVocFk = null)
    {
        $this->datePrecisionVocFk = $datePrecisionVocFk;

        return $this;
    }

    /**
     * Get datePrecisionVocFk
     *
     * @return \Bbees\E3sBundle\Entity\Voc
     */
    public function getDatePrecisionVocFk()
    {
        return $this->datePrecisionVocFk;
    }

    /**
     * Set statutSqcAssVocFk
     *
     * @param \Bbees\E3sBundle\Entity\Voc $statutSqcAssVocFk
     *
     * @return SequenceAssemblee
     */
    public function setStatutSqcAssVocFk(\Bbees\E3sBundle\Entity\Voc $statutSqcAssVocFk = null)
    {
        $this->statutSqcAssVocFk = $statutSqcAssVocFk;

        return $this;
    }

    /**
     * Get statutSqcAssVocFk
     *
     * @return \Bbees\E3sBundle\Entity\Voc
     */
    public function getStatutSqcAssVocFk()
    {
        return $this->statutSqcAssVocFk;
    }

    /**
     * Add sequenceAssembleeEstRealisePar
     *
     * @param \Bbees\E3sBundle\Entity\SequenceAssembleeEstRealisePar $sequenceAssembleeEstRealisePar
     *
     * @return SequenceAssemblee
     */
    public function addSequenceAssembleeEstRealisePar(\Bbees\E3sBundle\Entity\SequenceAssembleeEstRealisePar $sequenceAssembleeEstRealisePar)
    {
        $sequenceAssembleeEstRealisePar->setSequenceAssembleeFk($this);
        $this->sequenceAssembleeEstRealisePars[] = $sequenceAssembleeEstRealisePar;

        return $this;
    }

    /**
     * Remove sequenceAssembleeEstRealisePar
     *
     * @param \Bbees\E3sBundle\Entity\SequenceAssembleeEstRealisePar $sequenceAssembleeEstRealisePar
     */
    public function removeSequenceAssembleeEstRealisePar(\Bbees\E3sBundle\Entity\SequenceAssembleeEstRealisePar $sequenceAssembleeEstRealisePar)
    {
        $this->sequenceAssembleeEstRealisePars->removeElement($sequenceAssembleeEstRealisePar);
    }

    /**
     * Get sequenceAssembleeEstRealisePars
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSequenceAssembleeEstRealisePars()
    {
        return $this->sequenceAssembleeEstRealisePars;
    }

    /**
     * Add sqcEstPublieDans
     *
     * @param \Bbees\E3sBundle\Entity\SqcEstPublieDans $sqcEstPublieDans
     *
     * @return SequenceAssemblee
     */
    public function addSqcEstPublieDans(\Bbees\E3sBundle\Entity\SqcEstPublieDans $sqcEstPublieDans)
    {
        $sqcEstPublieDans->setSequenceAssembleeFk($this);
        $this->sqcEstPublieDanss[] = $sqcEstPublieDans;

        return $this;
    }

    /**
     * Remove sqcEstPublieDans
     *
     * @param \Bbees\E3sBundle\Entity\SqcEstPublieDans $sqcEstPublieDans
     */
    public function removeSqcEstPublieDans(\Bbees\E3sBundle\Entity\SqcEstPublieDans $sqcEstPublieDans)
    {
        $this->sqcEstPublieDanss->removeElement($sqcEstPublieDans);
    }

    /**
     * Get sqcEstPublieDanss
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSqcEstPublieDanss()
    {
        return $this->sqcEstPublieDanss;
    }

    /**
     * Add especeIdentifiee
     *
     * @param \Bbees\E3sBundle\Entity\EspeceIdentifiee $especeIdentifiee
     *
     * @return SequenceAssemblee
     */
    public function addEspeceIdentifiee(\Bbees\E3sBundle\Entity\EspeceIdentifiee $especeIdentifiee)
    {
        $especeIdentifiee->setSequenceAssembleeFk($this);
        $this->especeIdentifiees[] = $especeIdentifiee;

        return $this;
    }

    /**
     * Remove especeIdentifiee
     *
     * @param \Bbees\E3sBundle\Entity\EspeceIdentifiee $especeIdentifiee
     */
    public function removeEspeceIdentifiee(\Bbees\E3sBundle\Entity\EspeceIdentifiee $especeIdentifiee)
    {
        $this->especeIdentifiees->removeElement($especeIdentifiee);
    }

    /**
     * Get especeIdentifiees
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEspeceIdentifiees()
    {
        return $this->especeIdentifiees;
    }

    /**
     * Add estAligneEtTraite
     *
     * @param \Bbees\E3sBundle\Entity\EstAligneEtTraite $estAligneEtTraite
     *
     * @return SequenceAssemblee
     */
    public function addEstAligneEtTraite(\Bbees\E3sBundle\Entity\EstAligneEtTraite $estAligneEtTraite)
    {
        $estAligneEtTraite->setSequenceAssembleeFk($this);
        $this->estAligneEtTraites[] = $estAligneEtTraite;

        return $this;
    }

    /**
     * Remove estAligneEtTraite
     *
     * @param \Bbees\E3sBundle\Entity\EstAligneEtTraite $estAligneEtTraite
     */
    public function removeEstAligneEtTraite(\Bbees\E3sBundle\Entity\EstAligneEtTraite $estAligneEtTraite)
    {
        $this->estAligneEtTraites->removeElement($estAligneEtTraite);
    }

    /**
     * Get estAligneEtTraite
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEstAligneEtTraites()
    {
        return $this->estAligneEtTraites;
    }
    
    /**
     * Set geneVocFk
     *
     * @param integer $geneVocFk
     *
     * @return SequenceAssemblee
     */
    public function setGeneVocFk($geneVocFk)
    {
        $this->geneVocFk = $geneVocFk;

        return $this;
    }

    /**
     * Get geneVocFk
     *
     * @return integer
     */
    public function getGeneVocFk()
    {
        return $this->geneVocFk;
    }

    /**
     * Set individuFk
     *
     * @param integer $individuFk
     *
     * @return SequenceAssemblee
     */
    public function setIndividuFk($individuFk)
    {
        $this->individuFk = $individuFk;

        return $this;
    }

    /**
     * Get individuFk
     *
     * @return integer
     */
    public function getIndividuFk()
    {
        return $this->individuFk;
    }

}
