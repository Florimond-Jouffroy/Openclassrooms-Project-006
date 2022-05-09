<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Video
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column(type: 'integer')]
  private $id;

  #[ORM\Column(type: 'string', length: 255)]
  private $link;

  #[ORM\ManyToOne(targetEntity: Trick::class, inversedBy: 'videos')]
  private $trick;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getLink(): ?string
  {
    return $this->link;
  }

  public function setLink(string $link): self
  {
    $this->link = $link;

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

  #[ORM\PrePersist()]
  #[ORM\PreUpdate()]
  public function test()
  {

    if (preg_match('/src="(?<embedUrl>[^"]+)"/', $this->getLink(), $out)) {
      $this->setLink($out['embedUrl']);
    }
  }
}
