<?php

namespace Ilios\CoreBundle\Classes;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;

use Ilios\CoreBundle\Entity\LearningMaterialInterface;

/**
 * Class FileSystem
 * @package Ilios\CoreBundle\Classes
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
     * @var string
     */
    protected $iliosFileStorePath;
    
    /**
     * A filesystem object to work with
     * @var FileSystem
     */
    protected $fileSystem;
    
    public function __construct(SymfonyFileSystem $fs, $iliosFileStorePath)
    {
        $this->iliosFileStorePath = $iliosFileStorePath;
        $this->fileSystem = $fs;
        if (!$this->fileSystem->exists($this->iliosFileStorePath)) {
            $this->fileSystem->mkdir($this->iliosFileStorePath);
        }
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
     * @return File
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
        return $this->iliosFileStorePath . '/' . $relativePath;
    }
}
