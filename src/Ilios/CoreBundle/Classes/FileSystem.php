<?php

namespace Ilios\CoreBundle\Classes;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;

/**
 * Class FileSystem
 * @package Ilios\CoreBundle\Classes
 *
 */
class FileSystem
{
    /**
     * @var string
     */
    protected $fileStore;
    
    /**
     * A filesystem object to work with
     * @var FileSystem
     */
    protected $fileSystem;
    
    public function __construct($kernelRootDirectory)
    {
        $appTmpDirectory = realpath($kernelRootDirectory . '/../var/tmp');
        $this->fileSystem = new SymfonyFileSystem();
        $this->fileStore = $this->createUploadDirectory($appTmpDirectory);
    }
    
    /**
     * If necessary create the file upload directory
     * Checks to ensure we can write to this directory and throws
     * and excpetion if we can't
     * @var [string] the path to the app directory
     * @return [string] the path to the upload directory
     */
    protected function createUploadDirectory($appTmpDirectory)
    {
        $tmpUploads = $appTmpDirectory . '/' . 'uploads';
        if (!$this->fileSystem->exists($tmpUploads)) {
            $this->fileSystem->mkdir($tmpUploads);
        }
        
        return $tmpUploads;
    }
    
    /**
     * Get a file blob by hash
     * @param  File $file
     * @return string $hash
     */
    public function storeFile(File $file)
    {
        $hash = md5_file($file->getPathName());
        
        $file->move($this->fileStore, $hash);
        
        return $hash;
    }
    
    /**
     * Get a file blob by hash
     * @param  [string] $hash
     */
    public function deleteFile($hash)
    {
        $file = $this->getFile($hash);
        $this->fileSystem->remove($file->getPathname());
    }
    
    /**
     * Get a file blob by hash
     * @param  [string] $hash
     * @param  [string] $newPath
     */
    public function moveFile($hash, $newPath)
    {
        $file = $this->getFile($hash);
        $this->fileSystem->move($file->getPathname(), $newPath);
    }
    
    /**
     * Get a File from a hash
     * @param  [string] $hash
     * @return File
     */
    protected function getFile($hash)
    {
        return new File($this->fileStore . '/' . $hash);
    }
    
}
