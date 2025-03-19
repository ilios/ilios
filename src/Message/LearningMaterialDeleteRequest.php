<?php

declare(strict_types=1);

namespace App\Message;

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
