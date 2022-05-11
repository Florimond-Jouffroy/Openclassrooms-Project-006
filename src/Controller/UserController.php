<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Form\UserNamesType;
use App\Form\Trick\PictureType;
use App\Form\PictureProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
  private $em;
  public function __construct(EntityManagerInterface $em, private string $picturesUploadDirectory)
  {
    $this->em = $em;
  }

  #[Route('/user/profile', name: 'user_profile')]
  #[IsGranted('ROLE_USER')]
  public function profile(Request $request)
  {
    $user = $this->getUser();

    $form = $this->createForm(UserNamesType::class, $user);
    $form->handleRequest($request);


    $formPicture = $this->createForm(PictureProfileType::class, $user);
    $formPicture->handleRequest($request);

    if ($formPicture->isSubmitted() && $formPicture->isValid()) {
      $picture = $formPicture->get('pictureProfile')->getData();

      if ($picture->getFile() instanceof UploadedFile) {
        if (null !== $picture->getId()) {
          $picture->setName(sprintf('__UPDATING__%s', $picture->getName()));
        }
      }

      $this->em->persist($user);

      $this->em->flush();
    }
    // Modification de l'image de profile de l'utilisateur

    if ($form->isSubmitted() && $form->isValid()) {
      $this->em->persist($user);
      $this->em->flush();
    }

    return $this->render('user/profile.html.twig', [
      'form' => $form->createView(),
      'formPicture' => $formPicture->createView(),
      'user' => $user,
    ]);
  }
}
