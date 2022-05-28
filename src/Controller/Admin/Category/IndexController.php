<?php

namespace App\Controller\Admin\Category;

use App\Repository\CategoryRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{

  #[Route('/admin/categorys', name: 'admin_categorys')]
  public function index(CategoryRepository $categoryRepository)
  {
    $categorys = $categoryRepository->findAll();

    return $this->render('admin/category/categorys.html.twig', [
      'categorys' => $categorys,
      'current' => 'categorys'
    ]);
  }
}
