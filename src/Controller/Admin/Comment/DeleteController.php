<?php

namespace App\Controller\Admin\Comment;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DeleteController extends AbstractController
{
    public function __construct(private CommentRepository $commentRepository)
    {
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
