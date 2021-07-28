<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\DataImportRepositoryInterface;
use DateTime;
use Exception;

/**
 * A service for loading default application data from file into the database.
 *
 * @package App\Service
 */
class DefaultDataLoader
{
    public function __construct(public DataimportFileLocator $dataImportFileLocator)
    {
    }

    /**
     * @param DataImportRepositoryInterface $repository
     * @param string $type
     * @throws Exception
     */
    public function import(DataImportRepositoryInterface $repository, string $type): void
    {
        $filename = $type . '.csv';
        $path = $this->dataImportFileLocator->getDataFilePath($filename);
        $now = (new DateTime())->format('Y-m-d H:i:s');
        $i = 0;

        if (($handle = fopen($path, 'r')) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                $i++;
                // step over the first row
                // since it contains the field names
                if (1 === $i) {
                    continue;
                }
                $repository->import($data, $type);
            }
            // clean-up
            fclose($handle);
        }
    }
}
