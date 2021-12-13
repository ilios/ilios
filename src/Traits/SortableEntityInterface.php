<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Interface SortableEntityInterface
 */
interface SortableEntityInterface
{
    /**
     * @param int $position
     */
    public function setPosition($position);

    public function getPosition(): int;
}
