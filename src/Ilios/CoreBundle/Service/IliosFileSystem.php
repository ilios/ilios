<?php

namespace Ilios\CoreBundle\Service;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;

use Ilios\CoreBundle\Entity\LearningMaterialInterface;

/**
 * Class IliosFileSystem
 *
 */
class IliosFileSystem
{
    /**
     * New learning materials whos path is based on their
     * file hash are stored in this subdirectory of the
     * learning_material directory
     * @var string
     */
    const HASHED_LM_DIRECTORY = 'learning_materials/lm';

    /**
     * Lock files are stored in this directory
     * @var string
     */
    const LOCK_FILE_DIRECTORY = 'locks';

    /**
     * @var Config
     */
    protected $config;
    
    /**
     * A filesystem object to work with
     * @var FileSystem
     */
    protected $fileSystem;
    
    public function __construct(SymfonyFileSystem $fs, Config $config)
    {
        $this->fileSystem = $fs;
        $this->config = $config;
    }
    
    /**
     * Store a learning material file and return the relativePath
     * @param File $file
     * @param boolean $preserveOriginalFile
     * @return string $relativePath
     */
    public function storeLearningMaterialFile(File $file, $preserveOriginalFile = true)
    {
        $relativePath = $this->getLearningMaterialFilePath($file);
        $fullPath = $this->getPath($relativePath);
        $dir = dirname($fullPath);
        $this->fileSystem->mkdir($dir);
        if ($preserveOriginalFile) {
            $this->fileSystem->copy(
                $file->getPathname(),
                $fullPath,
                false
            );
        } else {
            if (!$this->fileSystem->exists($fullPath)) {
                $this->fileSystem->rename($file->getPathname(), $fullPath);
            }
        }

        return $relativePath;
    }
    
    /**
     * Store a learning material file and return the relativePath
     * @param File $file
     * @return string $relativePath
     */
    public function getLearningMaterialFilePath(File $file)
    {
        $hash = md5_file($file->getPathname());

        return self::HASHED_LM_DIRECTORY . '/' . substr($hash, 0, 2) . '/' . $hash;
    }
    
    /**
     * Remove a file from the filesystem by hash
     * @param  [string] $relativePath
     */
    public function removeFile($relativePath)
    {
        $this->fileSystem->remove($this->getPath($relativePath));
    }
    
    /**
     * Get a File from a hash
     * @param  [string] $relativePath
     * @return File|boolean
     */
    public function getFile($relativePath)
    {
        if ($this->fileSystem->exists($this->getPath($relativePath))) {
            return new File($this->getPath($relativePath));
        }
        
        return false;
    }
    
    /**
     * Get a symfony FIle for a path
     * @param  string $path
     * @return File
     */
    public function getSymfonyFileForPath($path)
    {
        return new File($path);
    }

    /**
     * Get if a learning material file path is valid
     * @param LearningMaterialInterface $lm
     *
     * @return boolean
     */
    public function checkLearningMaterialFilePath(LearningMaterialInterface $lm)
    {
        $relativePath = $lm->getRelativePath();
        $fullPath = $this->getPath($relativePath);

        return $this->fileSystem->exists($fullPath);
    }
    
    /**
     * Turns a relative path into an Ilios file store path.
     * @param  string $relativePath
     * @return string
     */
    protected function getPath($relativePath)
    {
        $iliosFileStorePath = $this->config->get('file_system_storage_path');
        return $iliosFileStorePath . '/' . $relativePath;
    }

    /**
     * Get the path for a lock file
     * @param string $name
     * @return string $relativePath
     */
    protected function getLockFilePath($name)
    {
        $safeName = preg_replace('/[^a-z0-9\._-]+/i', '-', $name);
        return self::LOCK_FILE_DIRECTORY . '/' . $safeName;
    }

    /**
     * Create a lock file
     * @param string $name
     */
    public function createLock($name)
    {
        if ($this->hasLock($name)) {
            return;
        }
        $relativePath = $this->getLockFilePath($name);
        $fullPath = $this->getPath($relativePath);
        $dir = dirname($fullPath);
        $this->fileSystem->mkdir($dir);
        if (!$this->fileSystem->exists($fullPath)) {
            $this->fileSystem->touch($fullPath);
        }
    }

    /**
     * Remove a lock file
     * @param string $name
     */
    public function releaseLock($name)
    {
        if (!$this->hasLock($name)) {
            return;
        }
        $relativePath = $this->getLockFilePath($name);
        $fullPath = $this->getPath($relativePath);
        if ($this->fileSystem->exists($fullPath)) {
            $this->fileSystem->remove($fullPath);
        }
    }

    /**
     * Check if a lock file exists
     * @param string $name
     * @return boolean
     */
    public function hasLock($name)
    {
        $relativePath = $this->getLockFilePath($name);
        $fullPath = $this->getPath($relativePath);

        return $this->fileSystem->exists($fullPath);
    }
}
