<?php

namespace App\Controller\User\Password;

use App\Form\PasswordRequestType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PasswordRequestRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordRequestRepository $passwordRequestRepository,
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    #[Route('/reset-password/{token}', name: 'user_resetPassword')]
    public function resetPassword(Request $request, string $token): Response
    {
        $passwordRequest = $this->passwordRequestRepository->findOneBy(['token' => $token]);

        $form = $this->createForm(PasswordRequestType::class);
        $form->handleRequest($request);

        if (null === $passwordRequest) {
            return $this->redirectToRoute('home');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->getData('password');
            $user = $this->userRepository->findOneBy(['email' => $passwordRequest->getEmail()]);

            $user->setPassword($this->userPasswordHasher->hashPassword($user, $newPassword['password']));

            $this->em->persist($user);
            $this->em->remove($passwordRequest);
            $this->em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('user/password/passwordRequest.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
