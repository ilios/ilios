<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\DataImportRepositoryInterface;
use DateTime;
use Exception;

/**
 * A service for loading default application data from file.
 *
 * @package App\Service
 */
class DefaultDataLoader
{
    public function __construct(public DataimportFileLocator $dataImportFileLocator)
    {
    }

    public function load(string $type): array
    {
        $filename = $type . '.csv';
        $path = $this->dataImportFileLocator->getDataFilePath($filename);
        $i = 0;
        $rhett = [];
        if (($handle = fopen($path, 'r')) !== false) {
            while (($data = fgetcsv($handle, escape: "\\")) !== false) {
                $i++;
                // step over the first row
                // since it contains the field names
                if (1 === $i) {
                    continue;
                }
                $rhett[] = $data;
            }
            // clean-up
            fclose($handle);
        }
        return $rhett;
    }
}
