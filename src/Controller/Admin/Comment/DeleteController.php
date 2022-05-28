<?php

namespace App\Controller\Admin\Comment;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteController extends AbstractController
{
  private $em;
  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  #[Route('/admin/comment/{id}/delete', name: 'admin_comment_delete')]
  public function delete(Comment $comment = null)
  {
    if (null === $comment) {
      throw new NotFoundHttpException('Comment not found !');
    }

    $this->em->remove($comment);
    $this->em->flush();

    return $this->redirectToRoute('admin_comments');
  }
}
