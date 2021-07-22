<?php

declare(strict_types=1);

namespace App\Repository;

/**
 * Interface for repositories that can bulk-import data.
 *
 * Interface DataImportRepositoryInterface
 * @package App\Repository
 */
interface DataImportRepositoryInterface
{
    /**
     * Imports a given set of records into their corresponding database table(s).
     *
     * @param array $data An associative array containing the data records to import.
     * @param string|null $type The type of data that's being imported.
     * @param string|null $now The current time and date as an ANSI SQL compatible string representation.
     */
    public function import(array $data, string $type = null, string $now = null): void;
}
