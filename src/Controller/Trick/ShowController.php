<?php

namespace App\Controller\Trick;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\Trick\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ShowController extends AbstractController
{
  private $commentRepository, $em;

  public function __construct(CommentRepository $commentRepository, EntityManagerInterface $em)
  {
    $this->commentRepository = $commentRepository;
    $this->em = $em;
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

    //comment form
    if ($this->getUser()) {

      $form = $this->createForm(CommentType::class, $comment = new Comment);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
        $comment->setTrick($trick)->setUser($this->getUser());
        $this->em->persist($comment);
        $this->em->flush();

        $this->addFlash('success', 'Your comment were added!');

        return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
      }
    }

    return $this->render('trick/show.html.twig', [
      'trick' => $trick,
      'form' => $form->createView(),
      'comments' => $query,
      'nbPages' => $nbPages,
      'currentPage' => $page,
    ]);
  }
}
