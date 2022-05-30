<?php

namespace App\Controller\Admin\Trick;

use App\Entity\Trick;
use App\Service\TrickService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteController extends AbstractController
{
  public function __construct(EntityManagerInterface $em, TrickService $trickService)
  {
    $this->em = $em;
    $this->trickService = $trickService;
  }

  #[Route('/admin/trick/{id}/delete', name: 'admin_trick_delete')]
  public function delete(Trick $trick = null)
  {

    if (null === $trick) {
      throw new NotFoundHttpException('Trick not found !');
    }

    $this->trickService->removeTrick($trick);

    return $this->redirectToRoute('admin_tricks');
  }
}