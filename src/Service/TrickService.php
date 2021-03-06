<?php

namespace App\Service;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use App\Utils\Strings;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Security;

class TrickService
{
    public function __construct(private EntityManagerInterface $em, private Security $security, private TrickRepository $trickRepository)
    {
    }

    public function compareCollection(ArrayCollection $originalCollection, PersistentCollection $collection): void
    {
        foreach ($originalCollection as $element) {
            if (false === $collection->contains($element)) {
                $this->em->remove($element);
            }
        }
    }

    public function removeTrick(Trick $trick): void
    {
        if (!($pictures = $trick->getPictures())->isEmpty()) {
            foreach ($pictures as $picture) {
                $this->em->remove($picture);
            }
            $this->em->flush();
        }

        $this->trickRepository->remove($trick);
    }

    public function addTrick(Trick $trick): void
    {
        if (false === ($pictures = $trick->getPictures())->isEmpty()) {
            foreach ($pictures as $picture) {
                if ($picture->getFile() instanceof UploadedFile) {
                    if (null === $picture->getId()) {
                        $picture->setTrick($trick);
                    } else {
                        $picture->setName(sprintf('__UPDATING__%s', $picture->getName()));
                    }
                } elseif (null === $picture->getName()) {
                    $pictures->removeElement($picture);
                }
            }
        }

        if (false === ($videos = $trick->getVideos())->isEmpty()) {
            foreach ($videos as $video) {
                if (null === $video->getLink()) {
                    $videos->removeElement($video);
                } else {
                    $video->setTrick($trick);
                }
            }
        }

        $trick->setSlug(Strings::slug($trick->getName()));

        if (null === $trick->getId()) {
            $trick->setUser($this->security->getUser());
            $this->em->persist($trick);
        }

        $this->em->flush();
    }
}
