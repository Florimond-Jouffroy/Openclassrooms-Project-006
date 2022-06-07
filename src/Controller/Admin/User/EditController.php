<?php

namespace App\Controller\Admin\User;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/admin/user/{id}/addAdmin', name: 'admin_user_addRoleAdmin')]
    #[Route('/admin/user/{id}/removeRoleAdmin', name: 'admin_user_removeRoleAdmin')]
    public function editRole(User $user, string $userRole = 'ROLE_ADMIN'): Response
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
