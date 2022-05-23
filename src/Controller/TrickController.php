<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\Trick\TrickType;
use App\Form\Trick\CommentType;
use App\Repository\CommentRepository;
use App\Service\TrickService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TrickController extends AbstractController
{

  private $em, $sluger, $commentRepository, $trickService;

  public function __construct(
    EntityManagerInterface $em,
    SluggerInterface $sluger,
    CommentRepository $commentRepository,
    TrickService $trickService
  ) {
    $this->em = $em;
    $this->sluger = $sluger;
    $this->commentRepository = $commentRepository;
    $this->trickService = $trickService;
  }


  #[Route('/trick/new', name: 'trick_new')]
  #[Route('/trick/{id}/edit', name: 'trick_edit')]
  #[IsGranted('ROLE_USER')]
  public function edit(Trick $trick = null, Request $request)
  {

    if (null === $trick) {
      $trick = new Trick();
    } else {

      if ($trick->getUser() !== $this->getUser()) {

        $this->addFlash('danger', "You haven't the rights!!");
        return $this->redirectToRoute('home');
      }

      $originalPictures = $this->trickService->initializeCollection($trick->getPictures());
      $originalVideos = $this->trickService->initializeCollection($trick->getVideos());
    }

    $form = $this->createForm(TrickType::class, $trick);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      if (null !== $trick->getId()) {
        $this->trickService->compareCollection($originalPictures, $trick->getPictures());
        $this->trickService->compareCollection($originalVideos, $trick->getVideos());
      }

      $this->trickService->uploadPictures($trick->getPictures(), $trick);

      foreach ($trick->getVideos() as $video) {
        if (null === $video->getLink()) {
          $trick->getVideos()->removeElement($video);
        }
        $video->setTrick($trick);
      }

      $this->em->persist($trick);
      $this->em->flush();

      $this->addFlash(
        'success',
        'Your changes were saved!'
      );

      return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
    }

    return $this->render('trick/edit.html.twig', ['form' => $form->createView()]);
  }

  #[Route('/trick/{id}/{page<\d+>?1}', name: 'trick_show')]
  public function show(Trick $trick, Request $request, $page = 1)
  {

    $limit = 5;
    if (is_null($page) || $page < 1) {
      $page = 1;
    }

    $nbComments = $trick->getComments()->count();
    $nbPages = ceil($nbComments / $limit);


    if ($page > $nbPages & $nbPages) {
      throw $this->createNotFoundException("cette page n'existe pas");
    }

    $query = $this->commentRepository->findAllCommentTrick($page, $limit, $trick);

    $comment = new Comment;
    $form = $this->createForm(CommentType::class, $comment);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $comment->setTrick($trick)->setUser($this->getUser());

      $this->em->persist($comment);
      $this->em->flush();

      $this->addFlash(
        'success',
        'Your comment were added!'
      );

      return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
    }

    return $this->render('trick/show.html.twig', [
      'trick' => $trick,
      'form' => $form->createView(),
      'comments' => $query,
      'nbPages' => $nbPages,
      'currentPage' => $page,
    ]);
  }

  #[Route('/trick/{id}/delete', name: 'trick_delete')]
  #[IsGranted('ROLE_USER')]
  public function delete(Trick $trick)
  {
    if (null === $trick) {
      throw new NotFoundHttpException('Trick not found !');
    }

    if ($this->getUser() === $user = $trick->getUser()) {


      foreach ($trick->getPictures() as $picture) {
        $this->em->remove($picture);
      }

      $this->em->flush();

      $this->em->remove($trick);
      $this->em->flush();

      $this->addFlash(
        'success',
        'Your trick were Deleted!'
      );

      return $this->redirectToRoute('home');
    }

    return $this->redirectToRoute('home');
  }
}
