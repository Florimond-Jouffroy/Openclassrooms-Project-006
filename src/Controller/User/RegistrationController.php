<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Entity\Picture;
use App\Form\RegistrationType;
use App\Repository\PictureRepository;
use App\Service\MailerService;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        private string $picturesUploadDirectory,
        private MailerService $mailer,
        private UserRepository $userRepository,
        private PictureRepository $pictureRepository,

    ) {
    }

    #[Route('/register', name: 'user_register')]
    public function register(Request $request, TokenGeneratorInterface $tokenGenerator): Response
    {
        $form = $this->createForm(RegistrationType::class, $user = new User());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
            $user->setValidationToken($tokenGenerator->generateToken());


            if ($form->get('pictureProfile')->getData('name')->getName() === null) {
                $picture = new Picture($this->picturesUploadDirectory);
                $picture->setName('default_profile.jpg')->setFilepath($this->picturesUploadDirectory . '/' . $picture->getName());
                $user->setPictureProfile($picture);
            }

            $url = $this->generateUrl(
                'user_validation',
                ['token' => $user->getValidationToken()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $emailParameters = [
                'from' => 'noreply@snowtrick.com',
                'to' => new Address($user->getEmail()),
                'subject' => 'Validate your account',
                'htmlTemplate' => 'email/user/validateAccount.html.twig',
            ];

            $contextParameters = [
                'url' => $url,
            ];

            $this->mailer->send($emailParameters, $contextParameters);

            $this->userRepository->add($user);

            return $this->redirectToRoute('home');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/validation/{token}', name: 'user_validation')]
    public function validateAccount($token): Response
    {
        $user = $this->userRepository->findOneBy(['validation_token' => $token]);

        if (null !== $user) {
            $user->setValidationToken('null');
            $this->em->flush();
        }

        return $this->redirectToRoute('home');
    }
}
