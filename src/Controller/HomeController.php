<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

  #[Route("/", name: "home")]
  public function home(TrickRepository $trickRepository)
  {

    $tricks = $trickRepository->findAll();
    return $this->render("home.html.twig", [
      'tricks' => $tricks
    ]);
  }
}
