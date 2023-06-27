<?php

/*
 * This file is part of the E3sBundle from the GOTIT project (Gene, Occurence and Taxa in Integrative Taxonomy)
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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user_db",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_user_db__username", columns={"username"})})
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"username"}, message="This username is already taken")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="user_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="user_name", type="string", length=255, nullable=false, unique=true)
   */
  private $username;

  /**
   * @Assert\NotBlank()
   * @Assert\Length(
   *      min = 8,
   *      max = 50,
   *      minMessage = "Your password  must be at least {{ limit }} characters long",
   *      maxMessage = "Your password  cannot be longer than {{ limit }} characters"
   * )
   */
  private $plainPassword;

  /**
   * @var string
   *
   * @ORM\Column(name="user_password", type="string", length=255, nullable=false)
   */
  private $password;

  /**
   * @var string
   *
   * @ORM\Column(name="user_email", type="string", length=255, nullable=true)
   */
  private $email;

  /**
   * @var string
   *
   * @ORM\Column(name="user_role", type="string")
   */
  private $role;

  /**
   * @var string
   *
   * @ORM\Column(name="salt", type="string", length=255, nullable=true)
   */
  private $salt;

  /**
   * @var string
   *
   * @ORM\Column(name="user_full_name", type="string", length=255, nullable=false)
   */
  private $name;

  /**
   * @var string
   *
   * @ORM\Column(name="user_institution", type="string", length=255, nullable=true)
   */
  private $institution;

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
   * @var int
   *
   * @ORM\Column(name="creation_user_name", type="bigint", nullable=true)
   */
  private $userCre;

  /**
   * @var int
   *
   * @ORM\Column(name="update_user_name", type="bigint", nullable=true)
   */
  private $userMaj;

  /**
   * @var int
   *
   * @ORM\Column(name="user_is_active", type="smallint")
   */
  private $isActive;

  /**
   * @var string
   *
   * @ORM\Column(name="user_comments", type="text", nullable=true)
   */
  private $commentaireUser;

  public function __construct() {
    $this->isActive = true;
    //$this->setRoles(array($this->role));
    // may not be needed, see section on salt below
    // $this->salt = md5(uniqid('', true));
  }

  public function eraseCredentials() {
  }

  /**
   * Get id
   *
   * @return \bigints
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set username
   *
   * @param string $username
   *
   * @return User
   */
  public function setUsername($username) {
    $this->username = $username;

    return $this;
  }

  /**
   * Get username
   *
   * @return string
   */
  public function getUsername() {
    return $this->username;
  }

  /**
   * Get user identifier
   * is used since 5.3 instead of deprecated getUsername
   *
   * @return string
   */
  public function getUserIdentifier(): string {
    return $this->username;
  }

  /**
   * Set set PlainPassword
   *
   * @param string $password
   *
   * @return User
   */
  public function setPlainPassword($password) {
    $this->plainPassword = $password;
  }

  /**
   * Get PlainPassword
   *
   * @return string
   */
  public function getPlainPassword() {
    return $this->plainPassword;
  }

  /**
   * Set password
   *
   * @param string $password
   *
   * @return User
   */
  public function setPassword($password) {
    $this->password = $password;

    return $this;
  }

  /**
   * Get password
   *
   * @return string
   */
  public function getPassword(): ?string {
    return $this->password;
  }

  /**
   * Set email
   *
   * @param string $email
   *
   * @return User
   */
  public function setEmail($email) {
    $this->email = $email;

    return $this;
  }

  /**
   * Get email
   *
   * @return string
   */
  public function getEmail() {
    return $this->email;
  }

  /**
   * Set salt
   *
   * @param string $salt
   *
   * @return User
   */
  public function setSalt($salt) {
    $this->salt = $salt;

    return $this;
  }

  /**
   * Get salt
   *
   * @return string
   */
  public function getSalt() {
    return null;
    //return $this->salt;
  }

  /**
   * Set dateCre
   *
   * @param \DateTime $dateCre
   *
   * @return User
   */
  public function setDateCre($dateCre) {
    $this->dateCre = $dateCre;

    return $this;
  }

  /**
   * Get dateCre
   *
   * @return \DateTime
   */
  public function getDateCre() {
    return $this->dateCre;
  }

  /**
   * Set dateMaj
   *
   * @param \DateTime $dateMaj
   *
   * @return User
   */
  public function setDateMaj($dateMaj) {
    $this->dateMaj = $dateMaj;

    return $this;
  }

  /**
   * Get dateMaj
   *
   * @return \DateTime
   */
  public function getDateMaj() {
    return $this->dateMaj;
  }

  /**
   * Set userCre
   *
   * @param integer $userCre
   *
   * @return User
   */
  public function setUserCre($userCre) {
    $this->userCre = $userCre;

    return $this;
  }

  /**
   * Get userCre
   *
   * @return integer
   */
  public function getUserCre() {
    return $this->userCre;
  }

  /**
   * Set userMaj
   *
   * @param integer $userMaj
   *
   * @return User
   */
  public function setUserMaj($userMaj) {
    $this->userMaj = $userMaj;

    return $this;
  }

  /**
   * Get userMaj
   *
   * @return integer
   */
  public function getUserMaj() {
    return $this->userMaj;
  }

  /**
   * Set isActive
   *
   * @param integer $isActive
   *
   * @return User
   */
  public function setIsActive($isActive) {
    $this->isActive = $isActive;

    return $this;
  }

  /**
   * Get isActive
   *
   * @return integer
   */
  public function getIsActive() {
    return $this->isActive;
  }

  /**
   * Set commentaireUser
   *
   * @param string $commentaireUser
   *
   * @return User
   */
  public function setCommentaireUser($commentaireUser) {
    $this->commentaireUser = $commentaireUser;

    return $this;
  }

  /**
   * Get commentaireUser
   *
   * @return string
   */
  public function getCommentaireUser() {
    return $this->commentaireUser;
  }

  /**
   * Set name
   *
   * @param string $name
   *
   * @return User
   */
  public function setName($name) {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set institution
   *
   * @param string $institution
   *
   * @return User
   */
  public function setInstitution($institution) {
    $this->institution = $institution;

    return $this;
  }

  /**
   * Get institution
   *
   * @return string
   */
  public function getInstitution() {
    return $this->institution;
  }

  /**
   * Get roles
   *
   * @return string[]
   */
  public function getRoles(): array {
    return [$this->role];
  }

  /**
   * Set role
   *
   * @param string $role
   *
   * @return User
   */
  public function setRole($role) {
    $this->role = $role;

    return $this;
  }

  /**
   * Get role
   *
   * @return string
   */
  public function getRole() {
    return $this->role;
  }
}
