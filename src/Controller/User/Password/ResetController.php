<?php

namespace App\Controller\User\Password;

use App\Service\MailerService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PasswordRequestRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetController extends AbstractController
{

  private $userRepository, $passwordRequestRepository, $em, $mailer, $passwordHasher;

  public function __construct(
    UserRepository $userRepository,
    PasswordRequestRepository $passwordRequestRepository,
    EntityManagerInterface $em,
    MailerService $mailer,
    UserPasswordHasherInterface $userPasswordHasher
  ) {
    $this->userRepository = $userRepository;
    $this->passwordRequestRepository = $passwordRequestRepository;
    $this->em = $em;
    $this->mailer = $mailer;
    $this->passwordHasher = $userPasswordHasher;
  }

  #[Route('/reset-password/{token}', name: "user_resetPassword")]
  public function resetPassword(Request $request, string $token)
  {
    $passwordRequest = $this->passwordRequestRepository->findOneBy(['token' => $token]);

    $form = $this->createForm(PasswordRequestType::class);
    $form->handleRequest($request);

    if ($passwordRequest === null) {
      return $this->redirectToRoute('home');
    }

    if ($form->isSubmitted() && $form->isValid()) {

      $newPassword = $form->getData('password');
      $user = $this->userRepository->findOneBy(['email' => $passwordRequest->getEmail()]);

      $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword['password']));

      $this->em->persist($user);
      $this->em->remove($passwordRequest);
      $this->em->flush();

      return $this->redirectToRoute('home');
    }

    return $this->render('user/password/passwordRequest.html.twig', [
      'form' => $form->createView()
    ]);
  }
}
