<?php

declare(strict_types=1);

namespace App\Traits;

use DateTime;

trait CreatedAtEntity
{
    protected DateTime $createdAt;

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }
}
