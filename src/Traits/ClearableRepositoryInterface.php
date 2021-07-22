<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Declares a method for deleting all records from a repository's corresponding database table.
 * @package App\Traits
 */
interface ClearableRepositoryInterface
{
    /**
     * Delete all records from underlying database table(s).
     */
    public function clearData(): void;
}
