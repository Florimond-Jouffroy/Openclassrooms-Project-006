<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function send(array $emailParameters, array $contextParameters): void
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
