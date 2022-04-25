<?php

namespace App\Controller;

use App\Form\LoginType;
use App\Form\EmailRequestType;
use App\Service\MailerService;
use App\Entity\PasswordRequest;
use App\Form\PasswordRequestType;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PasswordRequestRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class LoginController extends AbstractController
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

      $url = $this->generateUrl('user_resetPassword', ['token' => $passwordRequest->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);


      $emailParameters = array(
        'from' => 'noreply@snowtrick.com',
        'to' => new Address($userEmail),
        'subject' => "Reset Password",
        'htmlTemplate' => 'email/user/resetPassword.html.twig',
      );

      $contextParameters = array(
        'url' => $url,
      );

      $this->mailer->send($emailParameters, $contextParameters);

      return $this->redirectToRoute('home');
    }

    return $this->render('user/password/emailRequest.html.twig', [
      'form' => $form->createView()
    ]);
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
