<?php

namespace App\Controller\Admin\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteController extends AbstractController
{
  private  $em;
  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }


  #[Route('/admin/user/{id}/delete', name: 'admin_user_delete')]
  public function delete(User $user = null)
  {
    if (null === $user) {
      throw new NotFoundHttpException('User not found !');
    }

    if ($user === $this->getUser()) {
      $this->addFlash('danger', 'Vous ne pouvez pas supprimer cet utilisateur !');
    } else {
      $this->em->remove($user);
      $this->em->flush();
    }

    return $this->redirectToRoute('admin_users');
  }
}
