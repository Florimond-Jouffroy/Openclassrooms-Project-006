<?php

namespace App\Controller\Trick;

use App\Entity\Trick;
use App\Form\Trick\TrickType;
use App\Service\TrickService;
use App\Utils\PersistentCollectionTools;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditController extends AbstractController
{
    public function __construct(private TrickService $trickService)
    {
    }

    #[Route('/trick/new', name: 'trick_new')]
    #[Route('/trick/{id}/edit', name: 'trick_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(Trick $trick = null, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user->isAccountValidated()) {
            $this->addFlash('danger', "Vous devez valider votre compte pour faire cela !");
            return $this->redirectToRoute('home');
        }

        if (null !== $trick) {
            if (false === $trick->isHis($this->getUser())) {
                $this->addFlash('danger', "You haven't the rights!!");

                return $this->redirectToRoute('home');
            }

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

            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->render('trick/edit.html.twig', ['form' => $form->createView(), 'trick' => $trick]);
    }
}
