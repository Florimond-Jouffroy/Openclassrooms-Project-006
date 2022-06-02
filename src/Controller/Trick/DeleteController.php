<?php

namespace App\Controller\Trick;

use App\Entity\Trick;
use App\Service\TrickService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteController extends AbstractController
{
  private $em, $trickService;

  public function __construct(EntityManagerInterface $em, TrickService $trickService)
  {
    $this->em = $em;
    $this->trickService = $trickService;
  }

  #[Route('/trick/{id}/delete', name: 'trick_delete')]
  #[IsGranted('ROLE_USER')]
  public function delete(Trick $trick = null)
  {
    if (null === $trick) {
      throw new NotFoundHttpException('Trick not found !');
    }

    if ($trick->isHis($this->getUser())) {

      $this->trickService->removeTrick($trick);
      $this->addFlash('success', 'Your trick were Deleted!');
    } else {
      $this->addFlash('danger', "You haven't the rights!!");
    }

    return $this->redirectToRoute('home');
  }
}
