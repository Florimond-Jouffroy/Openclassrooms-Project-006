<?php

namespace  App\Controller\User\Password;

use App\Service\MailerService;
use App\Entity\PasswordRequest;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PasswordRequestRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ForgotController extends AbstractController
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
}