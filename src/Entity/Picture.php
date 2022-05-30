<?php

namespace App\Entity;

use App\Entity\Trick;
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
  private int $id;

  #[ORM\Column(type: 'string', length: 255)]
  private  string $name;

  #[ORM\ManyToOne(targetEntity: Trick::class, inversedBy: 'pictures')]
  private ?Trick $trick;

  private ?UploadedFile $file = null;

  #[ORM\Column(type: 'string', length: 255)]
  private string $filepath;

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

  public function getFilepath(): ?string
  {
    return $this->filepath;
  }

  public function setFilepath(string $filepath): self
  {
    $this->filepath = $filepath;

    return $this;
  }

  #[ORM\PrePersist()]
  #[ORM\PreUpdate()]
  public function upload(): void
  {

    if (null === $this->getFile()) {
      return;
    }


    $fileName =  uniqid();

    if (str_contains($this->name, '__UPDATING__')) {
      unlink($this->filepath);
      $this->name = str_replace('__UPDATING__', '', $this->name);
      $uploadTo = str_replace('/' . $this->name, '', $this->filepath);
    } else {
      $this->name = sprintf('%s.%s', $fileName, $this->getFile()->getClientOriginalExtension());
      $uploadTo = $this->pictureUploadDirectory;
    }



    $this->setFilepath($uploadTo . '/' . $this->name);
    $this->getFile()->move($uploadTo, $this->name);
    $this->file = null;
  }



  #[ORM\PostRemove()]
  public function onDelete()
  {

    unlink($this->filepath);
  }
}
