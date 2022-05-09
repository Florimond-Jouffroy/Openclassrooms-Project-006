<?php

namespace App\Controller;

use App\Form\UserNamesType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
  private $em;
  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  #[Route('/user/profile', name: 'user_profile')]
  #[IsGranted('ROLE_USER')]
  public function profile(Request $request)
  {
    $user = $this->getUser();

    $form = $this->createForm(UserNamesType::class, $user);
    $form->handleRequest($request);

    // Modification de l'image de profile de l'utilisateur

    if ($form->isSubmitted() && $form->isValid()) {
      $this->em->persist($user);
      $this->em->flush();
    }

    return $this->render('user/profile.html.twig', [
      'form' => $form->createView(),
      'user' => $user,
    ]);
  }
}
