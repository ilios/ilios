<?php

declare(strict_types=1);

namespace App\Message;

use DateTime;

class LearningMaterialIndexRequest
{
    protected DateTime $createdAt;

    public function __construct(private int $id)
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}
