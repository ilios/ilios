<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\SessionDeleteRequest;
use App\Service\Index\Curriculum;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SessionDeleteHandler
{
    public function __construct(
        private readonly Curriculum $curriculumIndex
    ) {
    }

    public function __invoke(SessionDeleteRequest $message): void
    {
        $this->curriculumIndex->deleteSession($message->getSessionId());
    }
}
