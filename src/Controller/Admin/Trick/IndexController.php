<?php


namespace App\Controller\Admin\Trick;

use App\Repository\TrickRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{


  #[Route('/admin/tricks', name: "admin_tricks")]
  public function index(TrickRepository $trickRepository)
  {

    $tricks = $trickRepository->findAll();

    return $this->render('admin/trick/tricks.html.twig', [
      'current' => 'tricks',
      'tricks' => $tricks,
    ]);
  }
}
