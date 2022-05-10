<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column(type: 'integer')]
  private $id;

  #[ORM\Column(type: 'string', length: 180, unique: true)]
  private $email;

  #[ORM\Column(type: 'json')]
  private $roles = [];

  #[ORM\Column(type: 'string')]
  private $password;

  #[ORM\Column(type: 'string', length: 255)]
  private $firstname;

  #[ORM\Column(type: 'string', length: 255)]
  private $lastname;

  #[ORM\Column(type: 'string', length: 255, nullable: true)]
  private $validation_token;

  #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class)]
  private $comments;

  #[ORM\OneToMany(mappedBy: 'user', targetEntity: Trick::class)]
  private $tricks;

  public function __construct()
  {
      $this->comments = new ArrayCollection();
      $this->tricks = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getEmail(): ?string
  {
    return $this->email;
  }

  public function setEmail(string $email): self
  {
    $this->email = $email;

    return $this;
  }

  /**
   * A visual identifier that represents this user.
   *
   * @see UserInterface
   */
  public function getUserIdentifier(): string
  {
    return (string) $this->email;
  }

  /**
   * @deprecated since Symfony 5.3, use getUserIdentifier instead
   */
  public function getUsername(): string
  {
    return (string) $this->email;
  }

  /**
   * @see UserInterface
   */
  public function getRoles(): array
  {
    $roles = $this->roles;
    // guarantee every user at least has ROLE_USER
    $roles[] = 'ROLE_USER';

    return array_unique($roles);
  }

  public function setRoles(array $roles): self
  {
    $this->roles = $roles;

    return $this;
  }

  /**
   * @see PasswordAuthenticatedUserInterface
   */
  public function getPassword(): string
  {
    return $this->password;
  }

  public function setPassword(string $password): self
  {
    $this->password = $password;

    return $this;
  }

  /**
   * Returning a salt is only needed, if you are not using a modern
   * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
   *
   * @see UserInterface
   */
  public function getSalt(): ?string
  {
    return null;
  }

  /**
   * @see UserInterface
   */
  public function eraseCredentials()
  {
    // If you store any temporary, sensitive data on the user, clear it here
    // $this->plainPassword = null;
  }

  public function getFirstname(): ?string
  {
    return $this->firstname;
  }

  public function setFirstname(string $firstname): self
  {
    $this->firstname = $firstname;

    return $this;
  }

  public function getLastname(): ?string
  {
    return $this->lastname;
  }

  public function setLastname(string $lastname): self
  {
    $this->lastname = $lastname;

    return $this;
  }

  public function getValidationToken(): ?string
  {
    return $this->validation_token;
  }

  public function setValidationToken(?string $validation_token): self
  {
    $this->validation_token = $validation_token;

    return $this;
  }

  public function isAccountValidated(): bool
  {
    return ($this->getValidationToken() === null ? true : false);
  }

  /**
   * @return Collection<int, Comment>
   */
  public function getComments(): Collection
  {
      return $this->comments;
  }

  public function addComment(Comment $comment): self
  {
      if (!$this->comments->contains($comment)) {
          $this->comments[] = $comment;
          $comment->setUser($this);
      }

      return $this;
  }

  public function removeComment(Comment $comment): self
  {
      if ($this->comments->removeElement($comment)) {
          // set the owning side to null (unless already changed)
          if ($comment->getUser() === $this) {
              $comment->setUser(null);
          }
      }

      return $this;
  }

  /**
   * @return Collection<int, Trick>
   */
  public function getTricks(): Collection
  {
      return $this->tricks;
  }

  public function addTrick(Trick $trick): self
  {
      if (!$this->tricks->contains($trick)) {
          $this->tricks[] = $trick;
          $trick->setUser($this);
      }

      return $this;
  }

  public function removeTrick(Trick $trick): self
  {
      if ($this->tricks->removeElement($trick)) {
          // set the owning side to null (unless already changed)
          if ($trick->getUser() === $this) {
              $trick->setUser(null);
          }
      }

      return $this;
  }
}
