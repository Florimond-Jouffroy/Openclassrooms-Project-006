<?php

namespace App\Entity;

use App\Entity\Trick;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentRepository;
use App\Entity\Traits\TimeStampableTrait;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Comment
{

  use TimeStampableTrait;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column(type: 'integer')]
  private int $id;

  #[ORM\Column(type: 'text')]
  private string $content;

  #[ORM\ManyToOne(targetEntity: Trick::class, inversedBy: 'comments')]
  private Trick $trick;

  #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comments')]
  private User $user;

  #[ORM\Column(type: 'integer')]
  private int $valid = 0;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getContent(): ?string
  {
    return $this->content;
  }

  public function setContent(string $content): self
  {
    $this->content = $content;

    return $this;
  }

  public function getTrick(): ?Trick
  {
    return $this->trick;
  }

  public function setTrick(?Trick $trick): self
  {
    $this->trick = $trick;

    return $this;
  }

  public function getUser(): ?User
  {
    return $this->user;
  }

  public function setUser(?User $user): self
  {
    $this->user = $user;

    return $this;
  }

  public function getValid()
  {
    return $this->valid;
  }

  public function setValid($valid): self
  {
    $this->valid = $valid;

    return $this;
  }
}
