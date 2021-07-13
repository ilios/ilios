<?php

declare(strict_types=1);

namespace App\Traits;

use DateTime;

trait CreatedAtEntity
{
    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }
}
