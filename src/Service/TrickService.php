<?php


namespace App\Service;

use App\Entity\Trick;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TrickService
{
  private $em, $pictureService;

  public function __construct(EntityManagerInterface $em, PictureService $pictureService)
  {
    $this->em = $em;
    $this->pictureService = $pictureService;
  }

  //TODO A faire vérifier
  public function initializeCollection(PersistentCollection $collection): ArrayCollection
  {
    $newCollection = new ArrayCollection();
    foreach ($collection as $element) {
      $newCollection->add($element);
    }

    return $newCollection;
  }

  public function compareCollection(ArrayCollection $originalCollection, PersistentCollection $collection): void
  {
    foreach ($originalCollection as $element) {

      if (false === $collection->contains($element)) {
        $this->em->remove($element);
      }
    }
  }

  public function uploadPictures(PersistentCollection $collection, Trick $trick)
  {
    foreach ($collection as  $picture) {

      if (!$picture->getFile() instanceof UploadedFile && null === $picture->getName()) {
        $collection->removeElement($picture);
      }

      if ($picture->getFile() instanceof UploadedFile) {
        $this->pictureService->updatingPicture($picture);

        if (null === $picture->getId()) {
          $picture->setTrick($trick);
        }
      }
    }
  }
}
