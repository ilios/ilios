<?php

namespace Ilios\CoreBundle\Service;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;

/**
 * Class FileSystem
 *
 */
class TemporaryFileSystem
{
    /**
     * @var string
     */
    protected $temporaryFileStorePath;
    
    /**
     * A filesystem object to work with
     * @var FileSystem
     */
    protected $fileSystem;
    
    public function __construct(SymfonyFileSystem $fs, $kernelRootDirectory)
    {
        $tmpPath = realpath($kernelRootDirectory . '/../var/tmp');
        $this->fileSystem = $fs;
        $this->temporaryFileStorePath = $tmpPath . '/uploads';
        if (!$this->fileSystem->exists($this->temporaryFileStorePath)) {
            $this->fileSystem->mkdir($this->temporaryFileStorePath);
        }
        $this->fileSystem = $fs;
    }
    
    /**
     * Store a file and return the hash
     * @param  File $file
     * @return string $hash
     */
    public function storeFile(File $file)
    {
        $hash = md5_file($file->getPathname());
        if (!$this->fileSystem->exists($this->getPath($hash))) {
            $this->fileSystem->rename(
                $file->getPathname(),
                $this->getPath($hash)
            );
        }
        

        return $hash;
    }
    
    /**
     * Remove a file from the file system by hash
     * @param string $hash
     */
    public function removeFile($hash)
    {
        $this->fileSystem->remove($this->getPath($hash));
    }
    
    /**
     * Get a File from a hash
     * @param string $hash
     * @return File|boolean
     */
    public function getFile($hash)
    {
        if ($this->fileSystem->exists($this->getPath($hash))) {
            return new File($this->getPath($hash));
        }
        
        return false;
    }
    
    /**
     * Turn a relative path into an ilios file store path
     * @param  string $hash
     * @return string
     */
    protected function getPath($hash)
    {
        return $this->temporaryFileStorePath . '/' . $hash;
    }
}
