<?php

declare(strict_types=1);

namespace App\Traits;

use DateTime;

/**
 * Trait TimestampableEntity
 */
trait TimestampableEntity
{
    protected DateTime $updatedAt;

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function getClassName(): string
    {
        return __CLASS__;
    }
}
