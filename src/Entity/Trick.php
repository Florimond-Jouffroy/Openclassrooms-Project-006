<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TrickRepository;
use App\Entity\Traits\TimeStampableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: TrickRepository::class)]
#[UniqueEntity(
  'name',
  message: 'Ce nom est déjà utilisé, trouvez en un autre !'
)]
#[ORM\HasLifecycleCallbacks()]
class Trick
{
  use TimeStampableTrait;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column(type: 'integer')]
  private $id;

  #[ORM\Column(type: 'string', length: 255)]
  private $name;

  #[ORM\Column(type: 'string', length: 255)]
  private $slug;

  #[ORM\Column(type: 'text')]
  private $description;

  #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'tricks')]
  private $category;

  #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Picture::class, cascade: ['persist', 'remove'])]
  private $pictures;

  public function __construct()
  {
    $this->pictures = new ArrayCollection();
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

  public function getSlug(): ?string
  {
    return $this->slug;
  }

  public function setSlug(string $slug): self
  {
    $this->slug = $slug;

    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(string $description): self
  {
    $this->description = $description;

    return $this;
  }

  public function getCategory(): ?Category
  {
    return $this->category;
  }

  public function setCategory(?Category $category): self
  {
    $this->category = $category;

    return $this;
  }

  /**
   * @return Collection<int, Picture>
   */
  public function getPictures(): Collection
  {
    return $this->pictures;
  }

  public function addPicture(Picture $picture): self
  {
    if (!$this->pictures->contains($picture)) {
      $this->pictures[] = $picture;
      $picture->setTrick($this);
    }

    return $this;
  }

  public function removePicture(Picture $picture): self
  {
    if ($this->pictures->removeElement($picture)) {
      // set the owning side to null (unless already changed)
      if ($picture->getTrick() === $this) {
        $picture->setTrick(null);
      }
    }

    return $this;
  }

  public function getMainPicture()
  {
    return $this->pictures->first();
  }
}
