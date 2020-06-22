<?php

declare(strict_types=1);

namespace App\Message;

use DateTime;

class LearningMaterialIndexRequest
{
    private $id;
    private $createdAt;

    public function __construct(int $id)
    {
        $this->id = $id;
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
