<?php

namespace App\Controller\User;

use App\Form\PictureProfileType;
use App\Form\UserNamesType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em, private string $picturesUploadDirectory)
    {
        $this->em = $em;
    }

    #[Route('/user/profile', name: 'user_profile')]
    #[IsGranted('ROLE_USER')]
    public function profile(Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserNamesType::class, $user);
        $formPicture = $this->createForm(PictureProfileType::class, $user);

        $form->handleRequest($request);
        $formPicture->handleRequest($request);

        if ($formPicture->isSubmitted() && $formPicture->isValid()) {
            $picture = $formPicture->get('pictureProfile')->getData();

            if ($picture->getFile() instanceof UploadedFile) {
                if (null !== $picture->getId()) {
                    $picture->setName(sprintf('__UPDATING__%s', $picture->getName()));
                }
            }

            $this->em->flush();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
        }

        return $this->render('user/profile.html.twig', [
            'form' => $form->createView(),
            'formPicture' => $formPicture->createView(),
            'user' => $user,
        ]);
    }
}
