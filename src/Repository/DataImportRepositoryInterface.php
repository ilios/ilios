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
     * Imports a given record into its corresponding database table(s).
     *
     * @param array $data An associative array containing the data record to import.
     * @param string $type The type of data that's being imported.
     * @param array $referenceMap a map that holds references to already imported entities
     */
    public function import(array $data, string $type, array $referenceMap): array;
}
