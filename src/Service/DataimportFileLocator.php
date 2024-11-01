<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Config\FileLocator;

/**
 * Service class for locating dataimport files.
 *
 * Class DataimportFileLocator
 */
class DataimportFileLocator
{
    protected string $dir;

    protected FileLocator $fileLocator;

    public function __construct(string $kernelProjectDir)
    {
        $path = realpath($kernelProjectDir . '/config/dataimport');
        $this->fileLocator = new FileLocator();
        $this->setDirectoryPath($path);
    }

    /**
     * Finds and return the absolute path to a given dataimport file.
     *
     * @param string $fileName name of the data file.
     */
    public function getDataFilePath(string $fileName): string
    {
        return $this->fileLocator->locate($this->getDirectoryPath() . DIRECTORY_SEPARATOR . basename($fileName));
    }

    /**
     * @param string $dir The relative path to the data-files directory.
     */
    public function setDirectoryPath(string $dir): void
    {
        $this->dir = $dir;
    }

    public function getDirectoryPath(): string
    {
        return $this->dir;
    }
}
