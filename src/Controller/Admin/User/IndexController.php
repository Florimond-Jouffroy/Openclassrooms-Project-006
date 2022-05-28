<?php

namespace App\Controller\Admin\User;

use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{

  #[Route('/admin/users', name: 'admin_users')]
  public function index(UserRepository $userRepository)
  {
    $users = $userRepository->findAll();
    return $this->render('admin/user/users.html.twig', [
      'current' => 'users',
      'users' => $users
    ]);
  }
}
