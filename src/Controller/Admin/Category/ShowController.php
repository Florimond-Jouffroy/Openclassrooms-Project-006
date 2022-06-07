<?php

namespace App\Controller\Admin\Category;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ShowController extends AbstractController
{
    #[Route('/admin/category/{id}/show', name: 'admin_category_show')]
    public function show(Category $category = null)
    {
        if (null === $category) {
            throw new NotFoundHttpException('Category not found !');
        }

        return $this->render('admin/category/show.html.twig', [
            'current' => 'categorys',
            'category' => $category,
        ]);
    }
}
