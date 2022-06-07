<?php

namespace App\Controller\Admin\Comment;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteController extends AbstractController
{
  private $commentRepository;
  public function __construct(CommentRepository $commentRepository)
  {
    $this->commentRepository = $commentRepository;
  }

  #[Route('/admin/comment/{id}/delete', name: 'admin_comment_delete')]
  public function delete(Comment $comment = null)
  {
    if (null === $comment) {
      throw new NotFoundHttpException('Comment not found !');
    }

    $this->commentRepository->remove($comment);

    return $this->redirectToRoute('admin_comments');
  }
}
