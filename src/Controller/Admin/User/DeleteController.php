<?php

namespace App\Controller\Admin\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DeleteController extends AbstractController
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    #[Route('/admin/user/{id}/delete', name: 'admin_user_delete')]
    public function delete(User $user = null): Response
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
