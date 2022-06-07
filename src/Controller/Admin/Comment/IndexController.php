<?php

namespace App\Controller\Admin\Comment;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{

  #[Route('/admin/comments', name: 'admin_comments')]
  public function index(CommentRepository $commentRepository)
  {
    $validComments = array();
    $hiddenComments = array();
    $comments = $commentRepository->findAll();
    foreach ($comments as $comment) {
      if ($comment->isValid()) {
        array_push($validComments, $comment);
      } else {
        array_push($hiddenComments, $comment);
      }
    }

    return $this->render('admin/comment/comments.html.twig', [
      'current' => 'comments',
      'validComments' => $validComments,
      'hiddenComments' => $hiddenComments,
      'comments' => $comments
    ]);
  }
}
