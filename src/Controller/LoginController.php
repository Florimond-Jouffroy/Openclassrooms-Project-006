<?php

namespace App\Controller;

use App\Entity\PasswordRequest;
use App\Form\LoginType;
use App\Form\EmailRequestType;
use App\Repository\PasswordRequestRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{

  private $userRepository, $passwordRequestRepository, $em;

  public function __construct(UserRepository $userRepository, PasswordRequestRepository $passwordRequestRepository, EntityManagerInterface $em)
  {
    $this->userRepository = $userRepository;
    $this->passwordRequestRepository = $passwordRequestRepository;
    $this->em = $em;
  }

  #[Route('/login', name: 'user_login')]
  public function login(AuthenticationUtils $authenticationUtils)
  {
    $form = $this->createForm(LoginType::class, ['email' => $authenticationUtils->getLastUsername()]);

    return $this->render('user/login.html.twig', [
      'form' => $form->createView(),
      'error' => $authenticationUtils->getLastAuthenticationError()
    ]);
  }

  #[Route('/logout', name: 'user_logout')]
  public function logout()
  {
  }

  #[Route('/forgot-password', name: 'user_forgotPassword')]
  public function forgotPassword(Request $request, TokenGeneratorInterface $tokenGenerator)
  {
    $form = $this->createForm(EmailRequestType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $userEmail = $form->get('email')->getData();

      if ($this->userRepository->findOneBy(['email' => $userEmail]) === null) {
        return $this->redirectToRoute('home');
      }

      $passwordRequest = $this->passwordRequestRepository->findOneBy(['email' => $userEmail]);

      if ($passwordRequest === null || ($passwordRequest !== null && $passwordRequest->isStillValid() === false)) {

        if ($passwordRequest !== null && $passwordRequest->isStillValid() === false) {
          $this->em->remove($passwordRequest);
          $this->em->flush();
        }

        $passwordRequest = new PasswordRequest();
        $passwordRequest->updateTimestamps();
        $passwordRequest->setEmail($userEmail);
        $passwordRequest->setToken($tokenGenerator->generateToken());

        $this->em->persist($passwordRequest);
        $this->em->flush();
      }

      $url = $this->generateUrl('user_resetPassword', ['token' => $passwordRequest->getToken(), UrlGeneratorInterface::ABSOLUTE_URL]);

      // Construction de notre email

      // Envoie de notre email

      return $this->redirectToRoute('home');
    }

    return $this->render('user/password/emailRequest.html.twig', [
      'form' => $form
    ]);
  }
}
