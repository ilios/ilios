<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Interface SortableEntityInterface
 */
interface SortableEntityInterface
{
    public function setPosition(int $position): void;

    public function getPosition(): int;
}
