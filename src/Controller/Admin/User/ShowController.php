<?php

namespace App\Controller\Admin\User;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ShowController extends AbstractController
{

  #[Route('/admin/users/{id}', name: 'admin_user_show')]
  public function show(User $user)
  {
    return $this->render('admin/user/show.html.twig', [
      'current' => 'users',
      'user' => $user,
    ]);
  }
}