<?php

namespace Bbees\E3sBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * SequenceAssembleeExt
 *
 * @ORM\Table(name="sequence_assemblee_ext", uniqueConstraints={@ORM\UniqueConstraint(name="cu_sequence_assemblee_ext_cle_primaire", columns={"code_sqc_ass_ext"})}, indexes={@ORM\Index(name="IDX_9E9F85CF9D3CDB05", columns={"gene_voc_fk"}), @ORM\Index(name="IDX_9E9F85CFA30C442F", columns={"date_precision_voc_fk"}), @ORM\Index(name="IDX_9E9F85CF514D78E0", columns={"origine_sqc_ass_ext_voc_fk"}), @ORM\Index(name="IDX_9E9F85CF662D9B98", columns={"collecte_fk"}), @ORM\Index(name="IDX_9E9F85CF88085E0F", columns={"statut_sqc_ass_voc_fk"})})
 * @ORM\Entity
 */
class SequenceAssembleeExt
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="sequence_assemblee_ext_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code_sqc_ass_ext", type="string", length=1024, nullable=false)
     */
    private $codeSqcAssExt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation_sqc_ass_ext", type="date", nullable=true)
     */
    private $dateCreationSqcAssExt;

    /**
     * @var string
     *
     * @ORM\Column(name="accession_number_sqc_ass_ext", type="string", length=255, nullable=true)
     */
    private $accessionNumberSqcAssExt;

    /**
     * @var string
     *
     * @ORM\Column(name="code_sqc_ass_ext_alignement", type="string", length=1024, nullable=true)
     */
    private $codeSqcAssExtAlignement;

    /**
     * @var string
     *
     * @ORM\Column(name="num_individu_sqc_ass_ext", type="string", length=255, nullable=false)
     */
    private $numIndividuSqcAssExt;

    /**
     * @var string
     *
     * @ORM\Column(name="taxon_origine_sqc_ass_ext", type="string", length=255, nullable=true)
     */
    private $taxonOrigineSqcAssExt;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire_sqc_ass_ext", type="text", nullable=true)
     */
    private $commentaireSqcAssExt;

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
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gene_voc_fk", referencedColumnName="id")
     * })
     */
    private $geneVocFk;

    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="date_precision_voc_fk", referencedColumnName="id")
     * })
     */
    private $datePrecisionVocFk;

    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="origine_sqc_ass_ext_voc_fk", referencedColumnName="id")
     * })
     */
    private $origineSqcAssExtVocFk;

    /**
     * @var \Collecte
     *
     * @ORM\ManyToOne(targetEntity="Collecte")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="collecte_fk", referencedColumnName="id")
     * })
     */
    private $collecteFk;

    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="statut_sqc_ass_voc_fk", referencedColumnName="id")
     * })
     */
    private $statutSqcAssVocFk;


    /**
     * @ORM\OneToMany(targetEntity="SqcExtEstRealisePar", mappedBy="sequenceAssembleeExtFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $sqcExtEstRealisePars;
    
    /**
     * @ORM\OneToMany(targetEntity="SqcExtEstReferenceDans", mappedBy="sequenceAssembleeExtFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $sqcExtEstReferenceDanss;
  
    /**
     * @ORM\OneToMany(targetEntity="EspeceIdentifiee", mappedBy="sequenceAssembleeExtFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $especeIdentifiees;
    
    
    public function __construct()
    {
        $this->sqcExtEstRealisePars = new ArrayCollection();
    	$this->sqcExtEstReferenceDanss = new ArrayCollection();
        $this->especeIdentifiees = new ArrayCollection();
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
     * Set codeSqcAssExt
     *
     * @param string $codeSqcAssExt
     *
     * @return SequenceAssembleeExt
     */
    public function setCodeSqcAssExt($codeSqcAssExt)
    {
        $this->codeSqcAssExt = $codeSqcAssExt;

        return $this;
    }

    /**
     * Get codeSqcAssExt
     *
     * @return string
     */
    public function getCodeSqcAssExt()
    {
        return $this->codeSqcAssExt;
    }

    /**
     * Set dateCreationSqcAssExt
     *
     * @param \DateTime $dateCreationSqcAssExt
     *
     * @return SequenceAssembleeExt
     */
    public function setDateCreationSqcAssExt($dateCreationSqcAssExt)
    {
        $this->dateCreationSqcAssExt = $dateCreationSqcAssExt;

        return $this;
    }

    /**
     * Get dateCreationSqcAssExt
     *
     * @return \DateTime
     */
    public function getDateCreationSqcAssExt()
    {
        return $this->dateCreationSqcAssExt;
    }

    /**
     * Set accessionNumberSqcAssExt
     *
     * @param string $accessionNumberSqcAssExt
     *
     * @return SequenceAssembleeExt
     */
    public function setAccessionNumberSqcAssExt($accessionNumberSqcAssExt)
    {
        $this->accessionNumberSqcAssExt = $accessionNumberSqcAssExt;

        return $this;
    }

    /**
     * Get accessionNumberSqcAssExt
     *
     * @return string
     */
    public function getAccessionNumberSqcAssExt()
    {
        return $this->accessionNumberSqcAssExt;
    }

    /**
     * Set codeSqcAssExtAlignement
     *
     * @param string $codeSqcAssExtAlignement
     *
     * @return SequenceAssembleeExt
     */
    public function setCodeSqcAssExtAlignement($codeSqcAssExtAlignement)
    {
        $this->codeSqcAssExtAlignement = $codeSqcAssExtAlignement;

        return $this;
    }

    /**
     * Get codeSqcAssExtAlignement
     *
     * @return string
     */
    public function getCodeSqcAssExtAlignement()
    {
        return $this->codeSqcAssExtAlignement;
    }

    /**
     * Set numIndividuSqcAssExt
     *
     * @param string $numIndividuSqcAssExt
     *
     * @return SequenceAssembleeExt
     */
    public function setNumIndividuSqcAssExt($numIndividuSqcAssExt)
    {
        $this->numIndividuSqcAssExt = $numIndividuSqcAssExt;

        return $this;
    }

    /**
     * Get numIndividuSqcAssExt
     *
     * @return string
     */
    public function getNumIndividuSqcAssExt()
    {
        return $this->numIndividuSqcAssExt;
    }

    /**
     * Set taxonOrigineSqcAssExt
     *
     * @param string $taxonOrigineSqcAssExt
     *
     * @return SequenceAssembleeExt
     */
    public function setTaxonOrigineSqcAssExt($taxonOrigineSqcAssExt)
    {
        $this->taxonOrigineSqcAssExt = $taxonOrigineSqcAssExt;

        return $this;
    }

    /**
     * Get taxonOrigineSqcAssExt
     *
     * @return string
     */
    public function getTaxonOrigineSqcAssExt()
    {
        return $this->taxonOrigineSqcAssExt;
    }

    /**
     * Set commentaireSqcAssExt
     *
     * @param string $commentaireSqcAssExt
     *
     * @return SequenceAssembleeExt
     */
    public function setCommentaireSqcAssExt($commentaireSqcAssExt)
    {
        $this->commentaireSqcAssExt = $commentaireSqcAssExt;

        return $this;
    }

    /**
     * Get commentaireSqcAssExt
     *
     * @return string
     */
    public function getCommentaireSqcAssExt()
    {
        return $this->commentaireSqcAssExt;
    }

    /**
     * Set dateCre
     *
     * @param \DateTime $dateCre
     *
     * @return SequenceAssembleeExt
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
     * @return SequenceAssembleeExt
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
     * @return SequenceAssembleeExt
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
     * @return SequenceAssembleeExt
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
     * Set geneVocFk
     *
     * @param \Bbees\E3sBundle\Entity\Voc $geneVocFk
     *
     * @return SequenceAssembleeExt
     */
    public function setGeneVocFk(\Bbees\E3sBundle\Entity\Voc $geneVocFk = null)
    {
        $this->geneVocFk = $geneVocFk;

        return $this;
    }

    /**
     * Get geneVocFk
     *
     * @return \Bbees\E3sBundle\Entity\Voc
     */
    public function getGeneVocFk()
    {
        return $this->geneVocFk;
    }

    /**
     * Set datePrecisionVocFk
     *
     * @param \Bbees\E3sBundle\Entity\Voc $datePrecisionVocFk
     *
     * @return SequenceAssembleeExt
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
     * Set origineSqcAssExtVocFk
     *
     * @param \Bbees\E3sBundle\Entity\Voc $origineSqcAssExtVocFk
     *
     * @return SequenceAssembleeExt
     */
    public function setOrigineSqcAssExtVocFk(\Bbees\E3sBundle\Entity\Voc $origineSqcAssExtVocFk = null)
    {
        $this->origineSqcAssExtVocFk = $origineSqcAssExtVocFk;

        return $this;
    }

    /**
     * Get origineSqcAssExtVocFk
     *
     * @return \Bbees\E3sBundle\Entity\Voc
     */
    public function getOrigineSqcAssExtVocFk()
    {
        return $this->origineSqcAssExtVocFk;
    }

    /**
     * Set collecteFk
     *
     * @param \Bbees\E3sBundle\Entity\Collecte $collecteFk
     *
     * @return SequenceAssembleeExt
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

    /**
     * Set statutSqcAssVocFk
     *
     * @param \Bbees\E3sBundle\Entity\Voc $statutSqcAssVocFk
     *
     * @return SequenceAssembleeExt
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
     * Add sqcExtEstRealisePar
     *
     * @param \Bbees\E3sBundle\Entity\SqcExtEstRealisePar $sqcExtEstRealisePar
     *
     * @return SequenceAssembleeExt
     */
    public function addSqcExtEstRealisePar(\Bbees\E3sBundle\Entity\SqcExtEstRealisePar $sqcExtEstRealisePar)
    {
        $sqcExtEstRealisePar->setSequenceAssembleeExtFk($this);
        $this->sqcExtEstRealisePars[] = $sqcExtEstRealisePar;

        return $this;
    }

    /**
     * Remove sqcExtEstRealisePar
     *
     * @param \Bbees\E3sBundle\Entity\SqcExtEstRealisePar $sqcExtEstRealisePar
     */
    public function removeSqcExtEstRealisePar(\Bbees\E3sBundle\Entity\SqcExtEstRealisePar $sqcExtEstRealisePar)
    {
        $this->sqcExtEstRealisePars->removeElement($sqcExtEstRealisePar);
    }

    /**
     * Get sqcExtEstRealisePars
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSqcExtEstRealisePars()
    {
        return $this->sqcExtEstRealisePars;
    }

    /**
     * Add sqcExtEstReferenceDans
     *
     * @param \Bbees\E3sBundle\Entity\SqcExtEstReferenceDans $sqcExtEstReferenceDans
     *
     * @return SequenceAssembleeExt
     */
    public function addSqcExtEstReferenceDans(\Bbees\E3sBundle\Entity\SqcExtEstReferenceDans $sqcExtEstReferenceDans)
    {
        $sqcExtEstReferenceDans->setSequenceAssembleeExtFk($this);
        $this->sqcExtEstReferenceDanss[] = $sqcExtEstReferenceDans;

        return $this;
    }

    /**
     * Remove sqcExtEstReferenceDans
     *
     * @param \Bbees\E3sBundle\Entity\SqcExtEstReferenceDans $sqcExtEstReferenceDans
     */
    public function removeSqcExtEstReferenceDans(\Bbees\E3sBundle\Entity\SqcExtEstReferenceDans $sqcExtEstReferenceDans)
    {
        $this->sqcExtEstReferenceDanss->removeElement($sqcExtEstReferenceDans);
    }

    /**
     * Get sqcExtEstReferenceDanss
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSqcExtEstReferenceDanss()
    {
        return $this->sqcExtEstReferenceDanss;
    }

    /**
     * Add especeIdentifiee
     *
     * @param \Bbees\E3sBundle\Entity\EspeceIdentifiee $especeIdentifiee
     *
     * @return SequenceAssembleeExt
     */
    public function addEspeceIdentifiee(\Bbees\E3sBundle\Entity\EspeceIdentifiee $especeIdentifiee)
    {
        $especeIdentifiee->setSequenceAssembleeExtFk($this);
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
}
