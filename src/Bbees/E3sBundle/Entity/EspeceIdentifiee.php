<?php

namespace Bbees\E3sBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * EspeceIdentifiee
 *
 * @ORM\Table(name="espece_identifiee", indexes={@ORM\Index(name="IDX_49D19C8DFB5F790", columns={"critere_identification_voc_fk"}), @ORM\Index(name="IDX_49D19C8DA30C442F", columns={"date_precision_voc_fk"}), @ORM\Index(name="IDX_49D19C8DCDD1F756", columns={"sequence_assemblee_ext_fk"}), @ORM\Index(name="IDX_49D19C8D40D80ECD", columns={"lot_materiel_ext_fk"}), @ORM\Index(name="IDX_49D19C8D54DBBD4D", columns={"lot_materiel_fk"}), @ORM\Index(name="IDX_49D19C8D7B09E3BC", columns={"referentiel_taxon_fk"}), @ORM\Index(name="IDX_49D19C8D5F2C6176", columns={"individu_fk"}), @ORM\Index(name="IDX_49D19C8D5BE90E48", columns={"sequence_assemblee_fk"})})
 * @ORM\Entity
 */
class EspeceIdentifiee
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="espece_identifiee_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_identification", type="date", nullable=true)
     */
    private $dateIdentification;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire_esp_id", type="text", nullable=true)
     */
    private $commentaireEspId;

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
     *   @ORM\JoinColumn(name="critere_identification_voc_fk", referencedColumnName="id", nullable=true)
     * })
     */
    private $critereIdentificationVocFk;

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
     * @var \SequenceAssembleeExt
     *
     * @ORM\ManyToOne(targetEntity="SequenceAssembleeExt", inversedBy="especeIdentifiees")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sequence_assemblee_ext_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     */
    private $sequenceAssembleeExtFk;

    /**
     * @var \LotMaterielExt
     *
     * @ORM\ManyToOne(targetEntity="LotMaterielExt", inversedBy="especeIdentifiees")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lot_materiel_ext_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     */
    private $lotMaterielExtFk;

    /**
     * @var \LotMateriel
     *
     * @ORM\ManyToOne(targetEntity="LotMateriel", inversedBy="especeIdentifiees")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lot_materiel_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     */
    private $lotMaterielFk;

    /**
     * @var \ReferentielTaxon
     *
     * @ORM\ManyToOne(targetEntity="ReferentielTaxon")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="referentiel_taxon_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $referentielTaxonFk;

    /**
     * @var \Individu
     *
     * @ORM\ManyToOne(targetEntity="Individu", inversedBy="especeIdentifiees")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="individu_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     */
    private $individuFk;

    /**
     * @var \SequenceAssemblee
     *
     * @ORM\ManyToOne(targetEntity="SequenceAssemblee", inversedBy="especeIdentifiees")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sequence_assemblee_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     */
    private $sequenceAssembleeFk;
    
    /**
     * @ORM\OneToMany(targetEntity="EstIdentifiePar", mappedBy="especeIdentifieeFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $estIdentifiePars;


    public function __construct()
    {
        $this->estIdentifiePars = new ArrayCollection();
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
     * Set dateIdentification
     *
     * @param \DateTime $dateIdentification
     *
     * @return EspeceIdentifiee
     */
    public function setDateIdentification($dateIdentification)
    {
        $this->dateIdentification = $dateIdentification;

        return $this;
    }

    /**
     * Get dateIdentification
     *
     * @return \DateTime
     */
    public function getDateIdentification()
    {
        return $this->dateIdentification;
    }

    /**
     * Set commentaireEspId
     *
     * @param string $commentaireEspId
     *
     * @return EspeceIdentifiee
     */
    public function setCommentaireEspId($commentaireEspId)
    {
        $this->commentaireEspId = $commentaireEspId;

        return $this;
    }

    /**
     * Get commentaireEspId
     *
     * @return string
     */
    public function getCommentaireEspId()
    {
        return $this->commentaireEspId;
    }

    /**
     * Set dateCre
     *
     * @param \DateTime $dateCre
     *
     * @return EspeceIdentifiee
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
     * @return EspeceIdentifiee
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
     * @return EspeceIdentifiee
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
     * @return EspeceIdentifiee
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
     * Set critereIdentificationVocFk
     *
     * @param \Bbees\E3sBundle\Entity\Voc $critereIdentificationVocFk
     *
     * @return EspeceIdentifiee
     */
    public function setCritereIdentificationVocFk(\Bbees\E3sBundle\Entity\Voc $critereIdentificationVocFk = null)
    {
        $this->critereIdentificationVocFk = $critereIdentificationVocFk;

        return $this;
    }

    /**
     * Get critereIdentificationVocFk
     *
     * @return \Bbees\E3sBundle\Entity\Voc
     */
    public function getCritereIdentificationVocFk()
    {
        return $this->critereIdentificationVocFk;
    }

    /**
     * Set datePrecisionVocFk
     *
     * @param \Bbees\E3sBundle\Entity\Voc $datePrecisionVocFk
     *
     * @return EspeceIdentifiee
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
     * Set sequenceAssembleeExtFk
     *
     * @param \Bbees\E3sBundle\Entity\SequenceAssembleeExt $sequenceAssembleeExtFk
     *
     * @return EspeceIdentifiee
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
     * Set lotMaterielExtFk
     *
     * @param \Bbees\E3sBundle\Entity\LotMaterielExt $lotMaterielExtFk
     *
     * @return EspeceIdentifiee
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
     * Set lotMaterielFk
     *
     * @param \Bbees\E3sBundle\Entity\LotMateriel $lotMaterielFk
     *
     * @return EspeceIdentifiee
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
     * Set referentielTaxonFk
     *
     * @param \Bbees\E3sBundle\Entity\ReferentielTaxon $referentielTaxonFk
     *
     * @return EspeceIdentifiee
     */
    public function setReferentielTaxonFk(\Bbees\E3sBundle\Entity\ReferentielTaxon $referentielTaxonFk = null)
    {
        $this->referentielTaxonFk = $referentielTaxonFk;

        return $this;
    }

    /**
     * Get referentielTaxonFk
     *
     * @return \Bbees\E3sBundle\Entity\ReferentielTaxon
     */
    public function getReferentielTaxonFk()
    {
        return $this->referentielTaxonFk;
    }

    /**
     * Set individuFk
     *
     * @param \Bbees\E3sBundle\Entity\Individu $individuFk
     *
     * @return EspeceIdentifiee
     */
    public function setIndividuFk(\Bbees\E3sBundle\Entity\Individu $individuFk = null)
    {
        $this->individuFk = $individuFk;

        return $this;
    }

    /**
     * Get individuFk
     *
     * @return \Bbees\E3sBundle\Entity\Individu
     */
    public function getIndividuFk()
    {
        return $this->individuFk;
    }

    /**
     * Set sequenceAssembleeFk
     *
     * @param \Bbees\E3sBundle\Entity\SequenceAssemblee $sequenceAssembleeFk
     *
     * @return EspeceIdentifiee
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
     * Add estIdentifiePar
     *
     * @param \Bbees\E3sBundle\Entity\estIdentifiePar $estIdentifiePar
     *
     * @return EspeceIdentifiee
     */
    public function addEstIdentifiePar(\Bbees\E3sBundle\Entity\estIdentifiePar $estIdentifiePar)
    {
        $estIdentifiePar->setEspeceIdentifieeFk($this);
        $this->estIdentifiePars[] = $estIdentifiePar;

        return $this;
    }

    /**
     * Remove estIdentifiePar
     *
     * @param \Bbees\E3sBundle\Entity\estIdentifiePar $estIdentifiePar
     */
    public function removeEstIdentifiePar(\Bbees\E3sBundle\Entity\estIdentifiePar $estIdentifiePar)
    {
        $this->estIdentifiePars->removeElement($estIdentifiePar);
    }

    /**
     * Get estIdentifiePars
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEstIdentifiePars()
    {
        return $this->estIdentifiePars;
    }
}
