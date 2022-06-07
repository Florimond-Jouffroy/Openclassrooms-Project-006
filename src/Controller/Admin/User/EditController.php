<?php

namespace App\Controller\Admin\User;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Length;

class EditController extends AbstractController
{

  private  $em;
  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  #[Route('/admin/user/{id}/addAdmin', name: "admin_user_addRoleAdmin")]
  #[Route('/admin/user/{id}/removeRoleAdmin', name: "admin_user_removeRoleAdmin")]
  public function editRole(User $user, String $userRole = 'ROLE_ADMIN')
  {

    $roles = new ArrayCollection($user->getRoles());

    if ($roles->contains($userRole)) {
      $roles->remove($userRole);
    } else {
      $roles->add($userRole);
    }

    $user->setRoles($roles->toArray());
    $this->em->flush();

    return $this->redirectToRoute('admin_user_show', ['id' => $user->getId()]);
  }
}
