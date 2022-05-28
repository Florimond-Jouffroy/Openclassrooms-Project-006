<?php

namespace App\Controller\Admin\Comment;

use App\Repository\CommentRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{

  #[Route('/admin/comments', name: 'admin_comments')]
  public function index(CommentRepository $commentRepository)
  {
    $commentsV = $commentRepository->findBy(['valid' => 0]);
    $commentsNV = $commentRepository->findBy(['valid' => 1]);
    $comments = $commentRepository->findAll();
    return $this->render('admin/comment/comments.html.twig', [
      'current' => 'comments',
      'commentsV' => $commentsV,
      'commentsNV' => $commentsNV,
      'comments' => $comments
    ]);
  }
}
