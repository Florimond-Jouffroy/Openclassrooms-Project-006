<?php

namespace App\Controller\Admin\Category;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EditController extends AbstractController
{
  private $em, $categoryRepository;
  public function __construct(EntityManagerInterface $em, CategoryRepository $categoryRepository)
  {
    $this->em = $em;
    $this->categoryRepository = $categoryRepository;
  }

  #[Route('/admin/category/new', name: 'admin_category_new')]
  #[Route('/admin/category/{id}/edit', name: 'admin_category_edit')]
  public function new(Request $request, Category $category = null)
  {
    if ($category === null) {
      $category = new Category;
    }

    $form = $this->createForm(CategoryType::class, $category);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      if ($category->getId() === null) {
        $this->categoryRepository->add($category);
      } else {
        $this->em->flush();
      }

      return $this->redirectToRoute('admin_categorys');
    }

    return $this->render('admin/category/new.html.twig', [
      'current' => 'categorys',
      'form' => $form->createView()
    ]);
  }
}
