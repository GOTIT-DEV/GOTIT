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

/**
 * Chromatogramme
 *
 * @ORM\Table(name="chromatogram", uniqueConstraints={@ORM\UniqueConstraint(name="uk_chromatogram__chromatogram_code", columns={"chromatogram_code"})}, indexes={@ORM\Index(name="IDX_FCB2DAB7286BBCA9", columns={"chromato_primer_voc_fk"}), @ORM\Index(name="IDX_FCB2DAB7206FE5C0", columns={"chromato_quality_voc_fk"}), @ORM\Index(name="IDX_FCB2DAB7E8441376", columns={"institution_fk"}), @ORM\Index(name="IDX_FCB2DAB72B63D494", columns={"pcr_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Chromatogramme
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="chromatogram_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="chromatogram_code", type="string", length=255, nullable=false)
     */
    private $codeChromato;

    /**
     * @var string
     *
     * @ORM\Column(name="chromatogram_number", type="string", length=255, nullable=false)
     */
    private $numYas;

    /**
     * @var string
     *
     * @ORM\Column(name="chromatogram_comments", type="text", nullable=true)
     */
    private $commentaireChromato;

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
     *   @ORM\JoinColumn(name="chromato_primer_voc_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $primerChromatoVocFk;

    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="chromato_quality_voc_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $qualiteChromatoVocFk;

    /**
     * @var \Etablissement
     *
     * @ORM\ManyToOne(targetEntity="Etablissement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="institution_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $etablissementFk;

    /**
     * @var \Pcr
     *
     * @ORM\ManyToOne(targetEntity="Pcr")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pcr_fk", referencedColumnName="id", nullable=false)
     * })
     */
    private $pcrFk;

    /**
     * @var string
     *
     */
    private $codeChromatoSpecificite;

    
    
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
     * Set codeChromato
     *
     * @param string $codeChromato
     *
     * @return Chromatogramme
     */
    public function setCodeChromato($codeChromato)
    {
        $this->codeChromato = $codeChromato;

        return $this;
    }

    /**
     * Get codeChromato
     *
     * @return string
     */
    public function getCodeChromato()
    {
        return $this->codeChromato;
    }

    /**
     * Set numYas
     *
     * @param string $numYas
     *
     * @return Chromatogramme
     */
    public function setNumYas($numYas)
    {
        $this->numYas = $numYas;

        return $this;
    }

    /**
     * Get numYas
     *
     * @return string
     */
    public function getNumYas()
    {
        return $this->numYas;
    }

    /**
     * Set commentaireChromato
     *
     * @param string $commentaireChromato
     *
     * @return Chromatogramme
     */
    public function setCommentaireChromato($commentaireChromato)
    {
        $this->commentaireChromato = $commentaireChromato;

        return $this;
    }

    /**
     * Get commentaireChromato
     *
     * @return string
     */
    public function getCommentaireChromato()
    {
        return $this->commentaireChromato;
    }

    /**
     * Set dateCre
     *
     * @param \DateTime $dateCre
     *
     * @return Chromatogramme
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
     * @return Chromatogramme
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
     * @return Chromatogramme
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
     * @return Chromatogramme
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
     * Set primerChromatoVocFk
     *
     * @param \App\Entity\Voc $primerChromatoVocFk
     *
     * @return Chromatogramme
     */
    public function setPrimerChromatoVocFk(\App\Entity\Voc $primerChromatoVocFk = null)
    {
        $this->primerChromatoVocFk = $primerChromatoVocFk;

        return $this;
    }

    /**
     * Get primerChromatoVocFk
     *
     * @return \App\Entity\Voc
     */
    public function getPrimerChromatoVocFk()
    {
        return $this->primerChromatoVocFk;
    }

    /**
     * Set qualiteChromatoVocFk
     *
     * @param \App\Entity\Voc $qualiteChromatoVocFk
     *
     * @return Chromatogramme
     */
    public function setQualiteChromatoVocFk(\App\Entity\Voc $qualiteChromatoVocFk = null)
    {
        $this->qualiteChromatoVocFk = $qualiteChromatoVocFk;

        return $this;
    }

    /**
     * Get qualiteChromatoVocFk
     *
     * @return \App\Entity\Voc
     */
    public function getQualiteChromatoVocFk()
    {
        return $this->qualiteChromatoVocFk;
    }

    /**
     * Set etablissementFk
     *
     * @param \App\Entity\Etablissement $etablissementFk
     *
     * @return Chromatogramme
     */
    public function setEtablissementFk(\App\Entity\Etablissement $etablissementFk = null)
    {
        $this->etablissementFk = $etablissementFk;

        return $this;
    }

    /**
     * Get etablissementFk
     *
     * @return \App\Entity\Etablissement
     */
    public function getEtablissementFk()
    {
        return $this->etablissementFk;
    }

    /**
     * Set pcrFk
     *
     * @param \App\Entity\Pcr $pcrFk
     *
     * @return Chromatogramme
     */
    public function setPcrFk(\App\Entity\Pcr $pcrFk = null)
    {
        $this->pcrFk = $pcrFk;

        return $this;
    }

    /**
     * Get pcrFk
     *
     * @return \App\Entity\Pcr
     */
    public function getPcrFk()
    {
        return $this->pcrFk;
    }
    
 
    /**
     * Get CodeChromatoSpecificite
     *
     * @return string
     */
    public function getCodeChromatoSpecificite()
    {
        $specificite = $this->pcrFk->getSpecificiteVocFk()->getCode();
        $codeChromato = $this->codeChromato;
        $this->codeChromatoSpecificite = $codeChromato.'|'.$specificite;
        return $this->codeChromatoSpecificite;
    }
    
}
