<?php

namespace App\Controller\Admin\Category;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private CategoryRepository $categoryRepository)
    {
    }

    #[Route('/admin/category/new', name: 'admin_category_new')]
    #[Route('/admin/category/{id}/edit', name: 'admin_category_edit')]
    public function new(Request $request, Category $category = null): Response
    {
        if (null === $category) {
            $category = new Category();
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null === $category->getId()) {
                $this->categoryRepository->add($category);
            }

            $this->em->flush();

            return $this->redirectToRoute('admin_categorys');
        }

        return $this->render('admin/category/new.html.twig', [
            'current' => 'categorys',
            'form' => $form->createView(),
        ]);
    }
}
