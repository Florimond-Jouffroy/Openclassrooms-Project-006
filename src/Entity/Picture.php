<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PictureRepository;
use App\Entity\Traits\TimeStampableTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[ORM\Entity(repositoryClass: PictureRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Picture
{
  use TimeStampableTrait;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column(type: 'integer')]
  private $id;

  #[ORM\Column(type: 'string', length: 255)]
  private $name;

  #[ORM\ManyToOne(targetEntity: Trick::class, inversedBy: 'pictures')]
  private $trick;

  private ?UploadedFile $file = null;

  public function __construct(private string $pictureUploadDirectory)
  {
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function setName(string $name): self
  {
    $this->name = $name;

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

  public function getFile(): ?UploadedFile
  {
    return $this->file;
  }

  public function setFile(?UploadedFile $file)
  {
    $this->file = $file;
  }

  #[ORM\PrePersist()]
  #[ORM\PreUpdate()]
  public function upload(): void
  {

    if (null === $this->getFile()) {
      return;
    }


    $uploadTo = $this->pictureUploadDirectory;
    $fileName =  uniqid() . '.' . $this->getFile()->guessExtension();
    $this->setName($fileName);

    $this->getFile()->move($uploadTo, $fileName);
  }



  #[ORM\PostRemove()]
  public function onDelete()
  {
    unlink($this->pictureUploadDirectory . $this->name);
  }
}
