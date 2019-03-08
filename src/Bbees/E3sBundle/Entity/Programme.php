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

namespace Bbees\E3sBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Programme
 *
 * @ORM\Table(name="programme", uniqueConstraints={@ORM\UniqueConstraint(name="cu_programme_cle_primaire", columns={"code_programme"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Programme
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="programme_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code_programme", type="string", length=255, nullable=false)
     */
    private $codeProgramme;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_programme", type="string", length=1024, nullable=false)
     */
    private $nomProgramme;

    /**
     * @var string
     *
     * @ORM\Column(name="noms_responsables", type="text", nullable=false)
     */
    private $nomsResponsables;

    /**
     * @var string
     *
     * @ORM\Column(name="type_financeur", type="string", length=1024, nullable=true)
     */
    private $typeFinanceur;

    /**
     * @var integer
     *
     * @ORM\Column(name="annee_debut", type="bigint", nullable=true)
     */
    private $anneeDebut;

    /**
     * @var integer
     *
     * @ORM\Column(name="annee_fin", type="bigint", nullable=true)
     */
    private $anneeFin;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire_programme", type="text", nullable=true)
     */
    private $commentaireProgramme;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set codeProgramme
     *
     * @param string $codeProgramme
     *
     * @return Programme
     */
    public function setCodeProgramme($codeProgramme)
    {
        $this->codeProgramme = $codeProgramme;

        return $this;
    }

    /**
     * Get codeProgramme
     *
     * @return string
     */
    public function getCodeProgramme()
    {
        return $this->codeProgramme;
    }

    /**
     * Set nomProgramme
     *
     * @param string $nomProgramme
     *
     * @return Programme
     */
    public function setNomProgramme($nomProgramme)
    {
        $this->nomProgramme = $nomProgramme;

        return $this;
    }

    /**
     * Get nomProgramme
     *
     * @return string
     */
    public function getNomProgramme()
    {
        return $this->nomProgramme;
    }

    /**
     * Set nomsResponsables
     *
     * @param string $nomsResponsables
     *
     * @return Programme
     */
    public function setNomsResponsables($nomsResponsables)
    {
        $this->nomsResponsables = $nomsResponsables;

        return $this;
    }

    /**
     * Get nomsResponsables
     *
     * @return string
     */
    public function getNomsResponsables()
    {
        return $this->nomsResponsables;
    }

    /**
     * Set typeFinanceur
     *
     * @param string $typeFinanceur
     *
     * @return Programme
     */
    public function setTypeFinanceur($typeFinanceur)
    {
        $this->typeFinanceur = $typeFinanceur;

        return $this;
    }

    /**
     * Get typeFinanceur
     *
     * @return string
     */
    public function getTypeFinanceur()
    {
        return $this->typeFinanceur;
    }

    /**
     * Set anneeDebut
     *
     * @param integer $anneeDebut
     *
     * @return Programme
     */
    public function setAnneeDebut($anneeDebut)
    {
        $this->anneeDebut = $anneeDebut;

        return $this;
    }

    /**
     * Get anneeDebut
     *
     * @return integer
     */
    public function getAnneeDebut()
    {
        return $this->anneeDebut;
    }

    /**
     * Set anneeFin
     *
     * @param integer $anneeFin
     *
     * @return Programme
     */
    public function setAnneeFin($anneeFin)
    {
        $this->anneeFin = $anneeFin;

        return $this;
    }

    /**
     * Get anneeFin
     *
     * @return integer
     */
    public function getAnneeFin()
    {
        return $this->anneeFin;
    }

    /**
     * Set commentaireProgramme
     *
     * @param string $commentaireProgramme
     *
     * @return Programme
     */
    public function setCommentaireProgramme($commentaireProgramme)
    {
        $this->commentaireProgramme = $commentaireProgramme;

        return $this;
    }

    /**
     * Get commentaireProgramme
     *
     * @return string
     */
    public function getCommentaireProgramme()
    {
        return $this->commentaireProgramme;
    }

    /**
     * Set dateCre
     *
     * @param \DateTime $dateCre
     *
     * @return Programme
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
     * @return Programme
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
     * @return Programme
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
     * @return Programme
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
}
