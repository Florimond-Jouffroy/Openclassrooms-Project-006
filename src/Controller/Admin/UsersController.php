<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{


  private $userRepository, $em;

  public function __construct(UserRepository $userRepository, EntityManagerInterface $em)
  {
    $this->userRepository = $userRepository;
    $this->em = $em;
  }

  #[Route('/admin/users', name: 'admin_users')]
  public function index()
  {
    $users = $this->userRepository->findAll();
    return $this->render('admin/user/users.html.twig', [
      'current' => 'users',
      'users' => $users
    ]);
  }

  #[Route('/admin/users/{id}', name: 'admin_user_show')]
  public function show(User $user)
  {
    return $this->render('admin/user/show.html.twig', [
      'current' => 'users',
      'user' => $user,
    ]);
  }


  #[Route('/admin/user/{id}/addAdmin', name: "admin_user_addRoleAdmin")]
  public function addRoleAdmin(User $user)
  {

    $roles = $user->getRoles();
    array_push($roles, 'ROLE_ADMIN');
    $user->setRoles($roles);

    $this->em->persist($user);
    $this->em->flush();

    return $this->redirectToRoute('admin_user_show', ['id' => $user->getId()]);
  }

  #[Route('/admin/user/{id}/removeRoleAdmin', name: "admin_user_removeRoleAdmin")]
  public function removeRoleAdmin(User $user)
  {
    $roles = $user->getRoles();
    foreach ($roles as $key => $role) {
      if ($role === "ROLE_ADMIN") {
        unset($roles[$key]);
      }
    }
    $user->setRoles($roles);
    $this->em->persist($user);
    $this->em->flush();

    return $this->redirectToRoute('admin_user_show', ['id' => $user->getId()]);
  }
}
