<?php

namespace App\Controller\User\Password;

use App\Form\EmailRequestType;
use App\Service\MailerService;
use App\Entity\PasswordRequest;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PasswordRequestRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ForgotController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordRequestRepository $passwordRequestRepository,
        private EntityManagerInterface $em,
        private MailerService $mailer,
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    #[Route('/forgot-password', name: 'user_forgotPassword')]
    public function forgotPassword(Request $request, TokenGeneratorInterface $tokenGenerator): Response
    {
        $form = $this->createForm(EmailRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userEmail = $form->get('email')->getData();

            if (null === $this->userRepository->findOneBy(['email' => $userEmail])) {
                return $this->redirectToRoute('home');
            }

            $passwordRequest = $this->passwordRequestRepository->findOneBy(['email' => $userEmail]);

            if (null === $passwordRequest || (null !== $passwordRequest && false === $passwordRequest->isStillValid())) {
                if (null !== $passwordRequest && false === $passwordRequest->isStillValid()) {
                    $this->em->remove($passwordRequest);
                    $this->em->flush();
                }

                $passwordRequest = new PasswordRequest();
                $passwordRequest->updateTimestamps();
                $passwordRequest->setEmail($userEmail);
                $passwordRequest->setToken($tokenGenerator->generateToken());

                $this->em->persist($passwordRequest);
                $this->em->flush();
            }

            $url = $this->generateUrl('user_resetPassword', ['token' => $passwordRequest->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);

            $emailParameters = [
                'from' => 'noreply@snowtrick.com',
                'to' => new Address($userEmail),
                'subject' => 'Reset Password',
                'htmlTemplate' => 'email/user/resetPassword.html.twig',
            ];

            $contextParameters = [
                'url' => $url,
            ];

            $this->mailer->send($emailParameters, $contextParameters);

            return $this->redirectToRoute('home');
        }

        return $this->render('user/password/emailRequest.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
