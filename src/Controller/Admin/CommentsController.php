<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentsController extends AbstractController
{

  private $em;

  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  #[Route('/admin/comments', name: 'admin_comments')]
  public function index(CommentRepository $commentRepository)
  {
    $comments = $commentRepository->findAll();
    return $this->render('admin/comment/comments.html.twig', [
      'current' => 'comments',
      'comments' => $comments
    ]);
  }



  #[Route('/admin/comment/{id}/delete', name: 'admin_comment_delete')]
  public function delete(Comment $comment)
  {

    $this->em->remove($comment);
    $this->em->flush();

    return $this->redirectToRoute('admin_comments');
  }
}
