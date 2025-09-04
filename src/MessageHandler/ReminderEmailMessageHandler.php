<?php

namespace App\MessageHandler;
use App\Message\ReminderEmailMessage;
use App\Service\MailService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ReminderEmailMessageHandler
{
    public function __construct(private MailService $mailService) {}

    public function __invoke(ReminderEmailMessage $message)
    {
        // Ici on appelle ton service existant
        $this->mailService->sendReminderMail(
            $message->email,
            $message->eventName,

        );
    }
}
