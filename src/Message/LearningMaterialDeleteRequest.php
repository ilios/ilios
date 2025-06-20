<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async_priority_normal')]
class LearningMaterialDeleteRequest
{
    public function __construct(private readonly int $learningMaterialId)
    {
    }

    public function getLearningMaterialId(): int
    {
        return $this->learningMaterialId;
    }
}
