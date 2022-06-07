<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(array $emailParameters, array $contextParameters)
    {
        $email = (new TemplatedEmail())
      ->from($emailParameters['from'])
      ->to($emailParameters['to'])
      ->subject($emailParameters['subject'])
      ->htmlTemplate($emailParameters['htmlTemplate'])
      ->context($contextParameters);

        $this->mailer->send($email);
    }
}
