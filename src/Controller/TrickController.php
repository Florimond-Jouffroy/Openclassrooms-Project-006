<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\Trick\CommentType;
use App\Form\Trick\TrickType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TrickController extends AbstractController
{

  private $em, $sluger;

  public function __construct(
    EntityManagerInterface $em,
    SluggerInterface $sluger
  ) {
    $this->em = $em;
    $this->sluger = $sluger;
  }


  #[Route('/trick/new', name: 'trick_new')]
  public function new(Request $request)
  {
    $trick = new Trick();

    $form = $this->createForm(TrickType::class, $trick);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $trick->setSlug($this->sluger->slug($trick->getName()));


      foreach ($trick->getPictures() as $picture) {

        if (!$picture->getFile() instanceof UploadedFile) {
          $trick->getPictures()->removeElement($picture);
        }

        $picture->setTrick($trick);
      }

      foreach ($trick->getVideos() as $video) {
        $video->setTrick($trick);
      }

      $this->em->persist($trick);
      $this->em->flush();

      return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
    }


    return $this->render('trick/new.html.twig', [
      'form' => $form->createView(),
    ]);
  }

  #[Route('/trick/{id}', name: 'trick_show')]
  public function show(Trick $trick, Request $request)
  {
    $comment = new Comment;
    $form = $this->createForm(CommentType::class, $comment);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $comment->setTrick($trick)->setUser($this->getUser());

      $this->em->persist($comment);
      $this->em->flush();

      return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
    }



    return $this->render('trick/show.html.twig', [
      'trick' => $trick,
      'form' => $form->createView(),
    ]);
  }


  #[Route('/trick/{id}/edit', name: 'trick_edit')]
  public function edit(Trick $trick, Request $request)
  {

    $originalPictures = new ArrayCollection();
    $originalVideos = new ArrayCollection();

    foreach ($trick->getPictures() as $picture) {
      $originalPictures->add($picture);
    }

    foreach ($trick->getVideos() as $video) {
      $originalVideos->add($video);
    }


    $form = $this->createForm(TrickType::class, $trick);

    $form->handleRequest($request);


    if ($form->isSubmitted() && $form->isValid()) {

      foreach ($originalPictures as $picture) {

        if (false === $trick->getPictures()->contains($picture)) {
          $this->em->remove($picture);
        }
      }

      foreach ($originalVideos as $video) {

        if (false === $trick->getVideos()->contains($video)) {
          $this->em->remove($video);
        }
      }


      foreach ($trick->getPictures() as $picture) {

        if (!$picture->getFile() instanceof UploadedFile && null === $picture->getName()) {
          $trick->getPictures()->removeElement($picture);
        }

        if ($picture->getFile() instanceof UploadedFile) {
          if (null !== $picture->getId()) {
            $picture->setName(sprintf('__UPDATING__%s', $picture->getName()));
          } else {
            $picture->setTrick($trick);
          }
        }
      }

      foreach ($trick->getVideos() as $video) {
        if (null === $video->getLink()) {
          $trick->getVideos()->removeElement($video);
        }
        $video->setTrick($trick);
      }


      $this->em->flush();

      return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
    }

    return $this->render('trick/edit.html.twig', [
      'form' => $form->createView(),

    ]);
  }

  #[Route('/trick/{id}/delete', name: 'trick_delete')]
  public function delete(Trick $trick)
  {

    $this->em->remove($trick);
    $this->em->flush();

    return $this->redirectToRoute('home');
  }
}
