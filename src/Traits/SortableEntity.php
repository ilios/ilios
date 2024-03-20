<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Class SortableEntity
 */
trait SortableEntity
{
    protected int $position;

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
