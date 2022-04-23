<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class RegisterController extends AbstractController
{
  private $passwordHasher, $em;
  public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
  {
    $this->em = $em;
    $this->passwordHasher = $passwordHasher;
  }

  #[Route("/register", name: "user_register")]
  public function register(Request $request)
  {

    $user = new User;
    $form = $this->createForm(RegistrationType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
      $this->em->persist($user);
      $this->em->flush();
      return $this->redirectToRoute('home');
    }

    return $this->render("user/register.html.twig", [
      'form' => $form->createView()
    ]);
  }
}
