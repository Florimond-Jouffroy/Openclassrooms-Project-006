<?php

namespace App\Controller\Admin\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteController extends AbstractController
{
  private  $userRepository;
  public function __construct(UserRepository $userRepository)
  {
    $this->userRepository = $userRepository;
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
      $this->userRepository->remove($user);
    }

    return $this->redirectToRoute('admin_users');
  }
}
