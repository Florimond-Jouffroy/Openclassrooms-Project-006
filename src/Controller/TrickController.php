<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\Trick\TrickType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{

  private $em, $sluger;

  public function __construct(
    EntityManagerInterface $em,
    SluggerInterface $sluger
  ) {
    $this->em = $em;
    $this->sluger = $sluger;
  }


  #[Route('/trick/new', name: 'trick_new')]
  public function new(Request $request)
  {
    $trick = new Trick();

    $form = $this->createForm(TrickType::class, $trick);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $trick->setSlug($this->sluger->slug($trick->getName()));

      $this->em->persist($trick);
      $this->em->flush();

      return $this->redirectToRoute('home');
    }


    return $this->render('trick/new.html.twig', [
      'form' => $form->createView(),
    ]);
  }

  #[Route('/trick/{id}', name: 'trick_show')]
  public function show(Trick $trick)
  {
    return $this->render('trick/show.html.twig', [
      'trick' => $trick
    ]);
  }
}
