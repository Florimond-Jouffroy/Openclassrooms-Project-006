<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{


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
}
