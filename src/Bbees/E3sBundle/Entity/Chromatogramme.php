<?php

namespace Bbees\E3sBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Chromatogramme
 *
 * @ORM\Table(name="chromatogramme", uniqueConstraints={@ORM\UniqueConstraint(name="cu_chromatogramme_cle_primaire", columns={"code_chromato"})}, indexes={@ORM\Index(name="IDX_FCB2DAB7286BBCA9", columns={"primer_chromato_voc_fk"}), @ORM\Index(name="IDX_FCB2DAB7206FE5C0", columns={"qualite_chromato_voc_fk"}), @ORM\Index(name="IDX_FCB2DAB7E8441376", columns={"etablissement_fk"}), @ORM\Index(name="IDX_FCB2DAB72B63D494", columns={"pcr_fk"})})
 * @ORM\Entity
 */
class Chromatogramme
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="chromatogramme_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code_chromato", type="string", length=255, nullable=false)
     */
    private $codeChromato;

    /**
     * @var string
     *
     * @ORM\Column(name="num_yas", type="string", length=255, nullable=true)
     */
    private $numYas;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire_chromato", type="text", nullable=true)
     */
    private $commentaireChromato;

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
     *   @ORM\JoinColumn(name="primer_chromato_voc_fk", referencedColumnName="id")
     * })
     */
    private $primerChromatoVocFk;

    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="qualite_chromato_voc_fk", referencedColumnName="id")
     * })
     */
    private $qualiteChromatoVocFk;

    /**
     * @var \Etablissement
     *
     * @ORM\ManyToOne(targetEntity="Etablissement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="etablissement_fk", referencedColumnName="id")
     * })
     */
    private $etablissementFk;

    /**
     * @var \Pcr
     *
     * @ORM\ManyToOne(targetEntity="Pcr")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pcr_fk", referencedColumnName="id")
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
     * @param \Bbees\E3sBundle\Entity\Voc $primerChromatoVocFk
     *
     * @return Chromatogramme
     */
    public function setPrimerChromatoVocFk(\Bbees\E3sBundle\Entity\Voc $primerChromatoVocFk = null)
    {
        $this->primerChromatoVocFk = $primerChromatoVocFk;

        return $this;
    }

    /**
     * Get primerChromatoVocFk
     *
     * @return \Bbees\E3sBundle\Entity\Voc
     */
    public function getPrimerChromatoVocFk()
    {
        return $this->primerChromatoVocFk;
    }

    /**
     * Set qualiteChromatoVocFk
     *
     * @param \Bbees\E3sBundle\Entity\Voc $qualiteChromatoVocFk
     *
     * @return Chromatogramme
     */
    public function setQualiteChromatoVocFk(\Bbees\E3sBundle\Entity\Voc $qualiteChromatoVocFk = null)
    {
        $this->qualiteChromatoVocFk = $qualiteChromatoVocFk;

        return $this;
    }

    /**
     * Get qualiteChromatoVocFk
     *
     * @return \Bbees\E3sBundle\Entity\Voc
     */
    public function getQualiteChromatoVocFk()
    {
        return $this->qualiteChromatoVocFk;
    }

    /**
     * Set etablissementFk
     *
     * @param \Bbees\E3sBundle\Entity\Etablissement $etablissementFk
     *
     * @return Chromatogramme
     */
    public function setEtablissementFk(\Bbees\E3sBundle\Entity\Etablissement $etablissementFk = null)
    {
        $this->etablissementFk = $etablissementFk;

        return $this;
    }

    /**
     * Get etablissementFk
     *
     * @return \Bbees\E3sBundle\Entity\Etablissement
     */
    public function getEtablissementFk()
    {
        return $this->etablissementFk;
    }

    /**
     * Set pcrFk
     *
     * @param \Bbees\E3sBundle\Entity\Pcr $pcrFk
     *
     * @return Chromatogramme
     */
    public function setPcrFk(\Bbees\E3sBundle\Entity\Pcr $pcrFk = null)
    {
        $this->pcrFk = $pcrFk;

        return $this;
    }

    /**
     * Get pcrFk
     *
     * @return \Bbees\E3sBundle\Entity\Pcr
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
