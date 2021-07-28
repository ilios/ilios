<?php

declare(strict_types=1);

namespace App\Message;

class LearningMaterialIndexRequest
{
    public function __construct(private int $id)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
