<?php

namespace App\Entity;

use App\Entity\Traits\TimeStampableTrait;
use App\Repository\PasswordRequestRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PasswordRequestRepository::class)]
class PasswordRequest
{
  const VALID_TIME = 3600;

  use TimeStampableTrait;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column(type: 'integer')]
  private $id;

  #[ORM\Column(type: 'string', length: 255)]
  private $email;

  #[ORM\Column(type: 'string', length: 255)]
  private $token;

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

  public function getToken(): ?string
  {
    return $this->token;
  }

  public function setToken(string $token): self
  {
    $this->token = $token;

    return $this;
  }

  public function isStillValid(): bool
  {
    return (new DateTime())->getTimestamp() - $this->getCreatedAt()->getTimestamp() <= self::VALID_TIME;
  }
}
