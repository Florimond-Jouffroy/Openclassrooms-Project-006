<?php

namespace App\Controller\Admin;

use App\Entity\Trick;
use App\Form\Trick\TrickType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TricksController extends AbstractController
{
  private $em, $slugger;

  public function __construct(EntityManagerInterface $em, SluggerInterface $sluggerInterface)
  {
    $this->em = $em;
    $this->slugger = $sluggerInterface;
  }

  #[Route('/admin/tricks', name: "admin_tricks")]
  public function index(TrickRepository $trickRepository)
  {

    $tricks = $trickRepository->findAll();

    return $this->render('admin/trick/tricks.html.twig', [
      'current' => 'tricks',
      'tricks' => $tricks,
    ]);
  }

  #[Route('/admin/trick/new', name: 'admin_trick_new')]
  #[Route('/amdin/trick/edit/{id}', name: 'admin_trick_edit')]
  public function new(Request $request, Trick $trick = null)
  {
    if ($trick === null) {
      $trick = new Trick();
    }

    $form = $this->createForm(TrickType::class, $trick);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $trick->setSlug($this->slugger->slug($trick->getName()));
      $trick->setUser($this->getUser());

      foreach ($trick->getPictures() as $picture) {

        if (!$picture->getFile() instanceof UploadedFile) {
          $trick->getPictures()->removeElement($picture);
        }

        $picture->setTrick($trick);
      }

      foreach ($trick->getVideos() as $video) {
        $video->setTrick($trick);
      }

      $this->em->persist($trick);
      $this->em->flush();

      $this->addFlash(
        'success',
        'Your trick were added!'
      );

      return $this->redirectToRoute('admin_tricks');
    }

    return $this->render('admin/trick/edit.html.twig', [
      'current' => 'tricks',
      'form' => $form->createView()
    ]);
  }


  #[Route('/admin/trick/show/{id}', name: 'admin_trick_show')]
  public function show(Trick $trick)
  {
    return $this->render('admin/trick/show.html.twig', [
      'trick' => $trick,
      'current' => 'tricks'
    ]);
  }
}
