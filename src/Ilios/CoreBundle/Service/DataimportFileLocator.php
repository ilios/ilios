<?php
namespace Ilios\CoreBundle\Service;

use Symfony\Component\Config\FileLocator;

/**
 * Service class for locating dataimport files.
 *
 * Class DataimportFileLocator
 */
class DataimportFileLocator
{
    /**
     * @var string
     */
    protected $dir;

    /**
     * @var FileLocator
     */
    protected $fileLocator;

    /**
     * @var string Default data-files directory path.
     */
    const DATAIMPORT_FILE_DIR = '@IliosCoreBundle/Resources/dataimport';

    /**
     * @param FileLocator $fileLocator
     */
    public function __construct(FileLocator $fileLocator)
    {
        $this->fileLocator = $fileLocator;
        $this->setDirectoryPath(self::DATAIMPORT_FILE_DIR);
    }

    /**
     * Finds and return the absolute path to a given dataimport file.
     *
     * @param string $fileName name of the data file.
     * @return string the absolute path.
     */
    public function getDataFilePath($fileName)
    {
        $path = $this->fileLocator->locate($this->getDirectoryPath() . DIRECTORY_SEPARATOR . basename($fileName));
        return $path;
    }

    /**
     * @param string $dir The relative path to the data-files directory.
     */
    public function setDirectoryPath($dir)
    {
        $this->dir = $dir;
    }

    /**
     * @return string The relative path to the data-files directory.
     */
    public function getDirectoryPath()
    {
        return $this->dir;
    }
}
