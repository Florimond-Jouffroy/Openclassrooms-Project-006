<?php

namespace App\Controller\Admin\Comment;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ValidController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/admin/comment/{id}/validChange', name: 'admin_comment_validChange')]
    public function valid(Comment $comment)
    {
        if (null === $comment) {
            throw new NotFoundHttpException('Comment not found !');
        }

        switch ($comment->getValid()) {
            case 0:
                $comment->setValid(1);
                break;

            case 1:
                $comment->setValid(0);
                break;

            default:
                $comment->setValid(0);
                break;
        }

        $this->em->persist($comment);
        $this->em->flush();

        return $this->redirectToRoute('admin_comments');
    }
}
