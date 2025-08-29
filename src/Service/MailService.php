<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailService
{
    public function __construct(private MailerInterface $mailer) {}

    public function sendRegistrationMail(string $to, string $eventName): void
    {
        $email = (new Email())
            ->from('noreply@monsite.fr')
            ->to($to)
            ->subject('Inscription confirmée à la sortie')
            ->text("Tu es bien inscrit à la sortie : $eventName.")
            ->html("<p>Bonjour,</p><p>Tu es bien inscrit à la sortie : <strong>$eventName</strong>.</p>");

        $this->mailer->send($email);
    }
    public function sendUnregistrationMail(string $to, string $eventName): void
    {
        $email = (new Email())
            ->from('noreply@monsite.fr')
            ->to($to)
            ->subject('Désinscription confirmée à la sortie')
            ->text("Tu es bien désinscrit à la sortie : $eventName.")
            ->html("<p>Bonjour,</p><p>Tu es bien inscrit à la sortie : <strong>$eventName</strong>.</p>");

        $this->mailer->send($email);
    }
    public function sendReminderMail(string $to, string $eventName): void
    {
        $email = (new Email())
            ->from('noreply@monsite.fr')
            ->to($to)
            ->subject('Rappel : votre sortie approche !')
            ->text("Petit rappel : la sortie \"$eventName\" commence dans 48h ou moins" . ".")
            ->html("<p>Bonjour,</p>
                <p>Petit rappel : la sortie <strong>$eventName</strong> commence dans 48h ou moins <strong>" . "</strong>.</p>");

        $this->mailer->send($email);
    }


}
