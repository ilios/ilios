<?php

declare(strict_types=1);

namespace App\Message;

class SessionDeleteRequest
{
    public function __construct(private readonly int $sessionId)
    {
    }

    public function getSessionId(): int
    {
        return $this->sessionId;
    }
}
