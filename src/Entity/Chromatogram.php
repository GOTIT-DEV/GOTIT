<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use App\Entity\Abstraction\CompositeCodeEntityTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Chromatogram
 *
 * @ORM\Table(name="chromatogram",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_chromatogram__chromatogram_code", columns={"chromatogram_code"})},
 *  indexes={
 *      @ORM\Index(name="IDX_FCB2DAB7286BBCA9", columns={"chromato_primer_voc_fk"}),
 *      @ORM\Index(name="IDX_FCB2DAB7206FE5C0", columns={"chromato_quality_voc_fk"}),
 *      @ORM\Index(name="IDX_FCB2DAB7E8441376", columns={"institution_fk"}),
 *      @ORM\Index(name="IDX_FCB2DAB72B63D494", columns={"pcr_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"code"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Chromatogram extends AbstractTimestampedEntity {
	use CompositeCodeEntityTrait;

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
	 * @ORM\Column(name="chromatogram_code", type="string", length=255, nullable=false, unique=true)
	 */
	private $code;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="chromatogram_number", type="string", length=255, nullable=false)
	 */
	private $yasNumber;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="chromatogram_comments", type="text", nullable=true)
	 */
	private $comment;

	/**
	 * @var Voc
	 *
	 * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
	 * @ORM\JoinColumn(name="chromato_primer_voc_fk", referencedColumnName="id", nullable=false)
	 */
	private $primer;

	/**
	 * @var Voc
	 *
	 * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
	 * @ORM\JoinColumn(name="chromato_quality_voc_fk", referencedColumnName="id", nullable=false)
	 */
	private $quality;

	/**
	 * @var Institution
	 *
	 * @ORM\ManyToOne(targetEntity="Institution")
	 * @ORM\JoinColumn(name="institution_fk", referencedColumnName="id", nullable=false)
	 */
	private $institution;

	/**
	 * @var Pcr
	 *
	 * @ORM\ManyToOne(targetEntity="Pcr")
	 * @ORM\JoinColumn(name="pcr_fk", referencedColumnName="id", nullable=false)
	 */
	private $pcr;

	/**
	 * Get id
	 *
	 * @return string
	 */
	public function getId(): ?string {
		return $this->id;
	}

	/**
	 * Set code
	 *
	 * @param string $code
	 *
	 * @return Chromatogram
	 */
	public function setCode($code): Chromatogram {
		$this->code = $code;
		return $this;
	}

	/**
	 * Get code
	 *
	 * @return string
	 */
	public function getCode(): ?string {
		return $this->code;
	}

	private function _generateCode(): string {
		return join("|", [$this->getYasNumber(), $this->getPrimer()->getCode()]);
	}

	/**
	 * Set yasNumber
	 *
	 * @param string $yasNumber
	 *
	 * @return Chromatogram
	 */
	public function setYasNumber($yasNumber): Chromatogram {
		$this->yasNumber = $yasNumber;
		return $this;
	}

	/**
	 * Get yasNumber
	 *
	 * @return string
	 */
	public function getYasNumber(): ?string {
		return $this->yasNumber;
	}

	/**
	 * Set comment
	 *
	 * @param string $comment
	 *
	 * @return Chromatogram
	 */
	public function setComment($comment): Chromatogram {
		$this->comment = $comment;
		return $this;
	}

	/**
	 * Get comment
	 *
	 * @return string
	 */
	public function getComment(): ?string {
		return $this->comment;
	}

	/**
	 * Set primer
	 *
	 * @param Voc $primer
	 *
	 * @return Chromatogram
	 */
	public function setPrimer(Voc $primer = null): Chromatogram {
		$this->primer = $primer;
		return $this;
	}

	/**
	 * Get primer
	 *
	 * @return Voc
	 */
	public function getPrimer(): ?Voc {
		return $this->primer;
	}

	/**
	 * Set quality
	 *
	 * @param Voc $quality
	 *
	 * @return Chromatogram
	 */
	public function setQuality(Voc $quality = null): Chromatogram {
		$this->quality = $quality;
		return $this;
	}

	/**
	 * Get quality
	 *
	 * @return Voc
	 */
	public function getQuality(): ?Voc {
		return $this->quality;
	}

	/**
	 * Set institution
	 *
	 * @param Institution $institution
	 *
	 * @return Chromatogram
	 */
	public function setInstitution(
		Institution $institution = null,
	): Chromatogram {
		$this->institution = $institution;
		return $this;
	}

	/**
	 * Get institution
	 *
	 * @return Institution
	 */
	public function getInstitution(): ?Institution {
		return $this->institution;
	}

	/**
	 * Set pcr
	 *
	 * @param Pcr $pcr
	 *
	 * @return Chromatogram
	 */
	public function setPcr(Pcr $pcr = null): Chromatogram {
		$this->pcr = $pcr;
		return $this;
	}

	/**
	 * Get pcr
	 *
	 * @return Pcr
	 */
	public function getPcr(): ?Pcr {
		return $this->pcr;
	}

	/**
	 * Get CodeSpecificity
	 *
	 * @return string
	 */
	public function getCodeSpecificity(): ?string {
		$specificity = $this->pcr->getSpecificity()->getCode();
		return $this->code . "|" . $specificity;
	}
}
