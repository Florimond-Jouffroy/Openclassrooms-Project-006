<?php


namespace App\Controller\Admin\Category;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteController extends AbstractController
{

  private $em;
  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  #[Route('/admin/category/{id}/delete', name: 'admin_category_delete')]
  public function delete(Category $category = null)
  {
    if (null === $category) {
      throw new NotFoundHttpException('Category not found !');
    }

    $this->em->remove($category);
    $this->em->flush();

    return $this->redirectToRoute('admin_categorys');
  }
}
