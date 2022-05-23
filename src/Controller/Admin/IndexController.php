<?php


namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{

  #[Route('/admin', name: 'admin_index')]
  public function index()
  {



    return $this->render('admin/index.html.twig', [
      'current' => "index"
    ]);
  }
}
