<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegisterController extends AbstractController
{
  private $passwordHasher, $em, $mailer, $userRepository;
  public function __construct(
    EntityManagerInterface $em,
    UserPasswordHasherInterface $passwordHasher,
    MailerService $mailer,
    UserRepository $userRepository,
  ) {
    $this->em = $em;
    $this->passwordHasher = $passwordHasher;
    $this->mailer = $mailer;
    $this->userRepository = $userRepository;
  }

  #[Route("/register", name: "user_register")]
  public function register(Request $request, TokenGeneratorInterface $tokenGenerator)
  {

    $user = new User;
    $form = $this->createForm(RegistrationType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
      $user->setValidationToken($tokenGenerator->generateToken());

      $url = $this->generateUrl('user_validation', ['token' => $user->getValidationToken()], UrlGeneratorInterface::ABSOLUTE_URL);


      $emailParameters = array(
        'from' => 'noreply@snowtrick.com',
        'to' => new Address($user->getEmail()),
        'subject' => "Validate your account",
        'htmlTemplate' => 'email/user/validateAccount.html.twig',
      );

      $contextParameters = array(
        'url' => $url,
      );

      $this->mailer->send($emailParameters, $contextParameters);

      $this->em->persist($user);
      $this->em->flush();
      return $this->redirectToRoute('home');
    }

    return $this->render("user/register.html.twig", [
      'form' => $form->createView()
    ]);
  }

  #[Route('/user/validation/{token}', name: 'user_validation')]
  public function validateAccount($token)
  {
    $user = $this->userRepository->findOneBy(['token' => $token]);

    if ($user !== null) {
      $user->setValidationToken(null);
      $this->em->persist($user);
      $this->em->flush();
    }

    return $this->redirectToRoute('home');
  }
}
