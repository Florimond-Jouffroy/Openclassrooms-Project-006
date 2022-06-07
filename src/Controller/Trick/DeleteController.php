<?php

namespace App\Controller\Trick;

use App\Entity\Trick;
use App\Service\TrickService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DeleteController extends AbstractController
{
    public function __construct(public TrickService $trickService)
    {
    }

    #[Route('/trick/{id}/delete', name: 'trick_delete')]
    #[IsGranted('ROLE_USER')]
    public function delete(Trick $trick = null): Response
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
