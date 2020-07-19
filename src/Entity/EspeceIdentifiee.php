<?php

/*
 * This file is part of the E3sBundle.
 *
 * Authors : see information concerning authors of GOTIT project in file AUTHORS.md
 *
 * E3sBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * E3sBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with E3sBundle.  If not, see <https://www.gnu.org/licenses/>
 * 
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * EspeceIdentifiee
 *
 * @ORM\Table(name="identified_species", 
 *  indexes={
 *      @ORM\Index(name="IDX_801C3911B669F53D", columns={"type_material_voc_fk"}), 
 *      @ORM\Index(name="IDX_49D19C8DFB5F790", columns={"identification_criterion_voc_fk"}), 
 *      @ORM\Index(name="IDX_49D19C8DA30C442F", columns={"date_precision_voc_fk"}), 
 *      @ORM\Index(name="IDX_49D19C8DCDD1F756", columns={"external_sequence_fk"}), 
 *      @ORM\Index(name="IDX_49D19C8D40D80ECD", columns={"external_biological_material_fk"}), 
 *      @ORM\Index(name="IDX_49D19C8D54DBBD4D", columns={"internal_biological_material_fk"}), 
 *      @ORM\Index(name="IDX_49D19C8D7B09E3BC", columns={"taxon_fk"}), 
 *      @ORM\Index(name="IDX_49D19C8D5F2C6176", columns={"specimen_fk"}), 
 *      @ORM\Index(name="IDX_49D19C8D5BE90E48", columns={"internal_sequence_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class EspeceIdentifiee
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="identified_species_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="identification_date", type="date", nullable=true)
     */
    private $dateIdentification;

    /**
     * @var string
     *
     * @ORM\Column(name="identified_species_comments", type="text", nullable=true)
     */
    private $commentaireEspId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_of_creation", type="datetime", nullable=true)
     */
    private $dateCre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_of_update", type="datetime", nullable=true)
     */
    private $dateMaj;

    /**
     * @var integer
     *
     * @ORM\Column(name="creation_user_name", type="bigint", nullable=true)
     */
    private $userCre;

    /**
     * @var integer
     *
     * @ORM\Column(name="update_user_name", type="bigint", nullable=true)
     */
    private $userMaj;
    
    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_material_voc_fk", referencedColumnName="id", nullable=true)
     * })
     */
    private $typeMaterielVocFk;

    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="identification_criterion_voc_fk", referencedColumnName="id", nullable=false)
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
     *   @ORM\JoinColumn(name="external_sequence_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     */
    private $sequenceAssembleeExtFk;

    /**
     * @var \LotMaterielExt
     *
     * @ORM\ManyToOne(targetEntity="LotMaterielExt", inversedBy="especeIdentifiees")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="external_biological_material_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     */
    private $lotMaterielExtFk;

    /**
     * @var \LotMateriel
     *
     * @ORM\ManyToOne(targetEntity="LotMateriel", inversedBy="especeIdentifiees")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="internal_biological_material_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     */
    private $lotMaterielFk;

    /**
     * @var \ReferentielTaxon
     *
     * @ORM\ManyToOne(targetEntity="ReferentielTaxon")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="taxon_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $referentielTaxonFk;

    /**
     * @var \Individu
     *
     * @ORM\ManyToOne(targetEntity="Individu", inversedBy="especeIdentifiees")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="specimen_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     */
    private $individuFk;

    /**
     * @var \SequenceAssemblee
     *
     * @ORM\ManyToOne(targetEntity="SequenceAssemblee", inversedBy="especeIdentifiees")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="internal_sequence_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
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
     * @param \App\Entity\Voc $critereIdentificationVocFk
     *
     * @return EspeceIdentifiee
     */
    public function setCritereIdentificationVocFk(\App\Entity\Voc $critereIdentificationVocFk = null)
    {
        $this->critereIdentificationVocFk = $critereIdentificationVocFk;

        return $this;
    }

    /**
     * Get critereIdentificationVocFk
     *
     * @return \App\Entity\Voc
     */
    public function getCritereIdentificationVocFk()
    {
        return $this->critereIdentificationVocFk;
    }

    /**
     * Set datePrecisionVocFk
     *
     * @param \App\Entity\Voc $datePrecisionVocFk
     *
     * @return EspeceIdentifiee
     */
    public function setDatePrecisionVocFk(\App\Entity\Voc $datePrecisionVocFk = null)
    {
        $this->datePrecisionVocFk = $datePrecisionVocFk;

        return $this;
    }

    /**
     * Get datePrecisionVocFk
     *
     * @return \App\Entity\Voc
     */
    public function getDatePrecisionVocFk()
    {
        return $this->datePrecisionVocFk;
    }

    /**
     * Set sequenceAssembleeExtFk
     *
     * @param \App\Entity\SequenceAssembleeExt $sequenceAssembleeExtFk
     *
     * @return EspeceIdentifiee
     */
    public function setSequenceAssembleeExtFk(\App\Entity\SequenceAssembleeExt $sequenceAssembleeExtFk = null)
    {
        $this->sequenceAssembleeExtFk = $sequenceAssembleeExtFk;

        return $this;
    }

    /**
     * Get sequenceAssembleeExtFk
     *
     * @return \App\Entity\SequenceAssembleeExt
     */
    public function getSequenceAssembleeExtFk()
    {
        return $this->sequenceAssembleeExtFk;
    }

    /**
     * Set lotMaterielExtFk
     *
     * @param \App\Entity\LotMaterielExt $lotMaterielExtFk
     *
     * @return EspeceIdentifiee
     */
    public function setLotMaterielExtFk(\App\Entity\LotMaterielExt $lotMaterielExtFk = null)
    {
        $this->lotMaterielExtFk = $lotMaterielExtFk;

        return $this;
    }

    /**
     * Get lotMaterielExtFk
     *
     * @return \App\Entity\LotMaterielExt
     */
    public function getLotMaterielExtFk()
    {
        return $this->lotMaterielExtFk;
    }

    /**
     * Set lotMaterielFk
     *
     * @param \App\Entity\LotMateriel $lotMaterielFk
     *
     * @return EspeceIdentifiee
     */
    public function setLotMaterielFk(\App\Entity\LotMateriel $lotMaterielFk = null)
    {
        $this->lotMaterielFk = $lotMaterielFk;

        return $this;
    }

    /**
     * Get lotMaterielFk
     *
     * @return \App\Entity\LotMateriel
     */
    public function getLotMaterielFk()
    {
        return $this->lotMaterielFk;
    }

    /**
     * Set referentielTaxonFk
     *
     * @param \App\Entity\ReferentielTaxon $referentielTaxonFk
     *
     * @return EspeceIdentifiee
     */
    public function setReferentielTaxonFk(\App\Entity\ReferentielTaxon $referentielTaxonFk = null)
    {
        $this->referentielTaxonFk = $referentielTaxonFk;

        return $this;
    }

    /**
     * Get referentielTaxonFk
     *
     * @return \App\Entity\ReferentielTaxon
     */
    public function getReferentielTaxonFk()
    {
        return $this->referentielTaxonFk;
    }

    /**
     * Set individuFk
     *
     * @param \App\Entity\Individu $individuFk
     *
     * @return EspeceIdentifiee
     */
    public function setIndividuFk(\App\Entity\Individu $individuFk = null)
    {
        $this->individuFk = $individuFk;

        return $this;
    }

    /**
     * Get individuFk
     *
     * @return \App\Entity\Individu
     */
    public function getIndividuFk()
    {
        return $this->individuFk;
    }

    /**
     * Set sequenceAssembleeFk
     *
     * @param \App\Entity\SequenceAssemblee $sequenceAssembleeFk
     *
     * @return EspeceIdentifiee
     */
    public function setSequenceAssembleeFk(\App\Entity\SequenceAssemblee $sequenceAssembleeFk = null)
    {
        $this->sequenceAssembleeFk = $sequenceAssembleeFk;

        return $this;
    }

    /**
     * Get sequenceAssembleeFk
     *
     * @return \App\Entity\SequenceAssemblee
     */
    public function getSequenceAssembleeFk()
    {
        return $this->sequenceAssembleeFk;
    }

    /**
     * Add estIdentifiePar
     *
     * @param \App\Entity\estIdentifiePar $estIdentifiePar
     *
     * @return EspeceIdentifiee
     */
    public function addEstIdentifiePar(\App\Entity\estIdentifiePar $estIdentifiePar)
    {
        $estIdentifiePar->setEspeceIdentifieeFk($this);
        $this->estIdentifiePars[] = $estIdentifiePar;

        return $this;
    }

    /**
     * Remove estIdentifiePar
     *
     * @param \App\Entity\estIdentifiePar $estIdentifiePar
     */
    public function removeEstIdentifiePar(\App\Entity\estIdentifiePar $estIdentifiePar)
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

    /**
     * Set typeMaterielVocFk
     *
     * @param \App\Entity\Voc $typeMaterielVocFk
     *
     * @return EspeceIdentifiee
     */
    public function setTypeMaterielVocFk(\App\Entity\Voc $typeMaterielVocFk = null)
    {
        $this->typeMaterielVocFk = $typeMaterielVocFk;

        return $this;
    }

    /**
     * Get typeMaterielVocFk
     *
     * @return \App\Entity\Voc
     */
    public function getTypeMaterielVocFk()
    {
        return $this->typeMaterielVocFk;
    }
}
