<?php

namespace App\Controller\Admin\Trick;

use App\Entity\Trick;
use App\Form\Trick\TrickType;
use App\Service\TrickService;
use App\Utils\PersistentCollectionTools;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditController extends AbstractController
{
    public function __construct(private TrickService $trickService)
    {
    }

    #[Route('/admin/trick/new', name: 'admin_trick_new')]
    #[Route('/amdin/trick/edit/{id}', name: 'admin_trick_edit')]
    public function edit(Request $request, Trick $trick = null): Response
    {
        if (null !== $trick) {
            $originalPictures = PersistentCollectionTools::initializeCollection($trick->getPictures());
            $originalVideos = PersistentCollectionTools::initializeCollection($trick->getVideos());
        } else {
            $trick = new Trick();
        }

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $trick->getId()) {
                $this->trickService->compareCollection($originalPictures, $trick->getPictures());
                $this->trickService->compareCollection($originalVideos, $trick->getVideos());
            }

            $this->trickService->addTrick($trick);

            $this->addFlash('success', 'Your trick were added!');

            return $this->redirectToRoute('admin_tricks');
        }

        return $this->render('admin/trick/edit.html.twig', [
            'current' => 'tricks',
            'form' => $form->createView(),
        ]);
    }
}
