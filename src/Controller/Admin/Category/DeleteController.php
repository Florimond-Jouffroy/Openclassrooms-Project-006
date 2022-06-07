<?php

namespace App\Controller\Admin\Category;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DeleteController extends AbstractController
{
    public function __construct(private CategoryRepository $categoryRepository)
    {
    }

    #[Route('/admin/category/{id}/delete', name: 'admin_category_delete')]
    public function delete(Category $category = null)
    {
        if (null === $category) {
            throw new NotFoundHttpException('Category not found !');
        }

        $this->categoryRepository->remove($category);

        return $this->redirectToRoute('admin_categorys');
    }
}
