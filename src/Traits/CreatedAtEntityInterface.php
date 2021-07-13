<?php

declare(strict_types=1);

namespace App\Traits;

use DateTime;

interface CreatedAtEntityInterface
{
    /**
     * @return DateTime
     */
    public function getCreatedAt();

    public function setCreatedAt(DateTime $createdAt);
}
