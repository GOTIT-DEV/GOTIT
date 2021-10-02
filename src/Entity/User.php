<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user_db",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_user_db__username", columns={"user_name"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"username"}, message="This username is already taken")
 *
 * @ApiResource(
 *  collectionOperations={"get": {"normalization_context": {"groups": {"item", "user:list"}}}},
 *  itemOperations={"get": {"normalization_context": {"groups": {"item", "user:item"}}}},
 *  order={"name": "ASC"},
 *  paginationEnabled=false
 * )
 */
class User extends AbstractTimestampedEntity implements UserInterface, PasswordAuthenticatedUserInterface {
  /**
   * @var int
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="user_id_seq", allocationSize=1, initialValue=1)
   *
   * @Groups({"item"})
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="user_name", type="string", length=255, nullable=false, unique=true)
   */
  private $username;

  /**
   * @Assert\NotBlank
   * @Assert\Length(
   *      min=8,
   *      max=50,
   *      minMessage="Your password  must be at least {{ limit }} characters long",
   *      maxMessage="Your password  cannot be longer than {{ limit }} characters"
   * )
   */
  private $plainPassword;

  /**
   * @var string
   *
   * @ORM\Column(name="user_password", type="string", length=255, nullable=false)
   * @Assert\NotBlank(allowNull=true)
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
   *
   * @Groups({"item"})
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
   * @Groups({"item"})
   */
  private $name;

  /**
   * @var string
   *
   * @ORM\Column(name="user_institution", type="string", length=255, nullable=true)
   * @Groups({"item"})
   */
  private $institution;

  /**
   * @var int
   *
   * @ORM\Column(name="user_is_active", type="smallint")
   */
  private $isActive = 1;

  /**
   * @var string
   *
   * @ORM\Column(name="user_comments", type="text", nullable=true)
   */
  private $comment;

  /**
   * Overloads parent getMetadata to return a flat array structure
   * with only date information
   *
   * @Groups({"item"})
   */
  public function getMetadata(): array {
    // return parent::getMetadata();
    return [
      'creation' => $this->getMetaCreationDate(),
      'update' => $this->getMetaUpdateDate(),
    ];
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
   * Set isActive
   *
   * @param int $isActive
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
   * @return int
   */
  public function getIsActive() {
    return $this->isActive;
  }

  /**
   * Set comment
   *
   * @param string $comment
   *
   * @return User
   */
  public function setComment($comment) {
    $this->comment = $comment;

    return $this;
  }

  /**
   * Get comment
   *
   * @return string
   */
  public function getComment() {
    return $this->comment;
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
   * @return array
   */
  public function getRoles() {
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
