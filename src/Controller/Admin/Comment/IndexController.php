<?php

namespace App\Controller\Admin\Comment;

use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/admin/comments', name: 'admin_comments')]
    public function index(CommentRepository $commentRepository)
    {
        $validComments = [];
        $hiddenComments = [];
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
            'comments' => $comments,
        ]);
    }
}
