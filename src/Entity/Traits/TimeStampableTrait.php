<?php

namespace App\Entity\Traits;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Trait TimeStampableTrait.
 */
trait TimeStampableTrait
{
    #[ORM\Column(type: 'datetime')]
  private DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
  private Datetime $updatedAt;

    /**
     * @throws Exception
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt ?? new DateTime();
    }

    /**
     * @return $this
     */
    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt ?? new DateTime();
    }

    /**
     * @return $this
     */
    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
  #[ORM\PreUpdate]
  public function updateTimestamps(): void
  {
      $now = new DateTime();
      $this->setUpdatedAt($now);
      if (null === $this->getId()) {
          $this->setCreatedAt($now);
      }
  }
}
