<?php

declare(strict_types=1);

namespace App\Traits;

use DateTime;

interface TimestampableEntityInterface extends StringableEntityToIdInterface
{
    public function getUpdatedAt(): DateTime;

    public function setUpdatedAt(DateTime $updatedAt);

    public function getClassName(): string;
}
