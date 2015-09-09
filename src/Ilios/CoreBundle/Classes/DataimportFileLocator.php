<?php
namespace Ilios\CoreBundle\Classes;

use Symfony\Component\Config\FileLocator;

/**
 * Service class for locating dataimport files.
 *
 * Class DataimportFileLocator
 * @package Ilios\CoreBundle\Classes
 */
class DataimportFileLocator
{
    /**
     * @var FileLocator
     */
    protected $fileLocator;

    /**
     * @var string
     */
    const DATAIMPORT_FILE_DIR = '@IliosCoreBundle/Resources/dataimport';

    /**
     * @param FileLocator $fileLocator
     */
    public function __construct(FileLocator $fileLocator)
    {
        $this->fileLocator = $fileLocator;
    }

    /**
     * Finds and return the absolute path to a given dataimport file.
     *
     * @param string $fileName name of the data file.
     * @return string the absolute path.
     */
    public function getDataFilePath($fileName)
    {
        $path = $this->fileLocator->locate(self::DATAIMPORT_FILE_DIR . DIRECTORY_SEPARATOR . basename($fileName));
        return $path;
    }
}
