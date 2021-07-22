<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Defines a method for deleting all records from a repository's corresponding database table.
 * @package App\Traits
 */
trait ClearableRepository
{
    public function clearData(): void
    {
        $this->createQueryBuilder('a')->delete()->getQuery()->execute();
    }
}
