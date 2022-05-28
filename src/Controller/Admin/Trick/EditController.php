<?php

namespace App\Controller\Admin\Trick;

use App\Entity\Trick;
use App\Form\Trick\TrickType;
use App\Service\TrickService;
use App\Utils\PersistentCollectionTools;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EditController extends AbstractController
{
  private $trickService;
  public function __construct(TrickService $trickService)
  {
    $this->trickService = $trickService;
  }


  #[Route('/admin/trick/new', name: 'admin_trick_new')]
  #[Route('/amdin/trick/edit/{id}', name: 'admin_trick_edit')]
  public function edit(Request $request, Trick $trick = null)
  {
    if ($trick !== null) {
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
      'form' => $form->createView()
    ]);
  }
}
