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
     * @param string $type The type of data that's being imported.
     */
    public function import(array $data, string $type): void;
}
