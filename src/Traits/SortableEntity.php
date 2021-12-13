<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Class SortableEntity
 */
trait SortableEntity
{
    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
