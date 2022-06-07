<?php

namespace App\Controller\Admin\Comment;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use PhpParser\Node\Expr\FuncCall;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    #[Route('/admin/comments', name: 'admin_comments')]
    public function index(CommentRepository $commentRepository): Response
    {

        $comments = new ArrayCollection($commentRepository->findAll());

        $validComments = $comments->filter(function (Comment $comment) {
            return $comment->isValid();
        });
        $hiddenComments = $comments->filter(function (Comment $comment) {
            return !$comment->isValid();
        });

        return $this->render('admin/comment/comments.html.twig', [
            'current' => 'comments',
            'validComments' => $validComments,
            'hiddenComments' => $hiddenComments,
            'comments' => $comments,
        ]);
    }
}
