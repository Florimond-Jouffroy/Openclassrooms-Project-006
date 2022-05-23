<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Trick;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategorysController extends AbstractController
{

  private $em;

  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  #[Route('/admin/categorys', name: 'admin_categorys')]
  public function index(CategoryRepository $categoryRepository)
  {
    $categorys = $categoryRepository->findAll();

    return $this->render('admin/category/categorys.html.twig', [
      'categorys' => $categorys,
      'current' => 'categorys'
    ]);
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
      $this->em->persist($category);
      $this->em->flush();

      return $this->redirectToRoute('admin_categorys');
    }

    return $this->render('admin/category/new.html.twig', [
      'current' => 'categorys',
      'form' => $form->createView()
    ]);
  }

  #[Route('/admin/category/{id}/show', name: 'admin_category_show')]
  public function show(Category $category)
  {

    return $this->render('admin/category/show.html.twig', [
      'current' => 'categorys',
      'category' => $category
    ]);
  }


  #[Route('/admin/category/{id}/delete', name: 'admin_category_delete')]
  public function delete(Category $category)
  {
    $this->em->remove($category);
    $this->em->flush();

    return $this->redirectToRoute('admin_categorys');
  }
}
