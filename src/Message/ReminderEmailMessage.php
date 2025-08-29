<?php

namespace App\Message;

class ReminderEmailMessage
{
    public function __construct(
        public string $email,
        public string $eventName,
        public int $sortieId,
        public int $userId
    ) {}
}
