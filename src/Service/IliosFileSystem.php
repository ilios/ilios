<?php

namespace App\Service;

use App\Classes\LocalCachingFilesystemDecorator;
use App\Exception\IliosFilesystemException;
use Aws\S3\Exception\S3Exception;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\File\File;

use App\Entity\LearningMaterialInterface;

/**
 * Class IliosFileSystem
 *
 */
class IliosFileSystem
{
    /**
     * New learning materials who's path is based on their
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
     * Testing files are stored in this directory
     * @var string
     */
    const TEST_FILE_ROOT = 'crud_tests';

    /**
     * A filesystem object to work with
     * @var FilesystemInterface
     */
    protected $fileSystem;

    public function __construct(FilesystemInterface $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    /**
     *
     * Store a learning material file and return the relativePath
     * @param File $file
     * @return string $relativePath
     */
    public function storeLearningMaterialFile(File $file) : string
    {
        $relativePath = $this->getLearningMaterialFilePath($file);
        $stream = fopen($file->getPathname(), 'r+');
        $this->fileSystem->writeStream($relativePath, $stream);
        fclose($stream);

        return $relativePath;
    }
    
    /**
     * Store a learning material file and return the relativePath
     * @param File $file
     * @return string $relativePath
     */
    public function getLearningMaterialFilePath(File $file) : string
    {
        $hash = md5_file($file->getPathname());

        return self::HASHED_LM_DIRECTORY . '/' . substr($hash, 0, 2) . '/' . $hash;
    }
    
    /**
     * Remove a file from the filesystem by hash
     * @param  string $relativePath
     */
    public function removeFile(string $relativePath) : void
    {
        $this->fileSystem->delete($relativePath);
    }


    /**
     * Get a File from a hash
     * @param  string $relativePath
     * @return string | bool
     */
    public function getFileContents(string $relativePath)
    {
        if ($this->fileSystem->has($relativePath)) {
            return $this->fileSystem->read($relativePath);
        }

        return false;
    }

    /**
     * Get if a learning material file path is valid
     * @param LearningMaterialInterface $lm
     *
     * @return boolean
     */
    public function checkLearningMaterialFilePath(LearningMaterialInterface $lm) : bool
    {
        $relativePath = $lm->getRelativePath();
        return $this->fileSystem->has($relativePath);
    }

    /**
     * Get the path for a lock file
     * @param string $name
     * @return string $relativePath
     */
    protected function getLockFilePath(string $name) : string
    {
        $safeName = preg_replace('/[^a-z0-9\._-]+/i', '-', $name);
        return self::LOCK_FILE_DIRECTORY . '/' . $safeName;
    }

    /**
     * Create a lock file
     * @param string $name
     */
    public function createLock(string $name) : void
    {
        if ($this->hasLock($name)) {
            return;
        }
        $relativePath = $this->getLockFilePath($name);
        if (!$this->fileSystem->has($relativePath)) {
            $this->fileSystem->put($relativePath, 'LOCK');
        }
    }

    /**
     * Remove a lock file
     * @param string $name
     */
    public function releaseLock(string $name) : void
    {
        if (!$this->hasLock($name)) {
            return;
        }
        $relativePath = $this->getLockFilePath($name);
        if ($this->fileSystem->has($relativePath)) {
            $this->fileSystem->delete($relativePath);
        }
    }

    /**
     * Check if a lock file exists
     * @param string $name
     * @return boolean
     */
    public function hasLock(string $name) : bool
    {
        $relativePath = $this->getLockFilePath($name);

        return $this->fileSystem->has($relativePath);
    }

    /**
     * Wait for and then acquire a lock
     * @param string $name
     */
    public function waitForLock(string $name) : void
    {
        while ($this->hasLock($name)) {
            usleep(250);
        }
        $this->createLock($name);
    }

    /**
     * Test Create, Read, Update, Delete on our filesystem
     * @throws IliosFilesystemException
     */
    public function testCRUD() : void
    {

        $path = self::TEST_FILE_ROOT . '/test-file-' . uniqid();
        $contents = md5_file(__FILE__);

        try {
            //cleanup any existing test files
            $this->fileSystem->deleteDir(self::TEST_FILE_ROOT);
        } catch (FileNotFoundException $e) {
            //ignore this one
        }
        if ($this->fileSystem instanceof LocalCachingFilesystemDecorator) {
            $this->fileSystem->disableCache();
        }
        try {
            $this->fileSystem->write($path, $contents);
            $result = $this->fileSystem->read($path);
            if (!$result || $result !== $contents) {
                throw new IliosFilesystemException('Unable to Read from Filesystem');
            }
            $putResult = $this->fileSystem->put($path, $contents . $contents);
            $result = $this->fileSystem->read($path);
            if (!$putResult || !$result || $result !==  $contents . $contents) {
                throw new IliosFilesystemException('Unable to Update Filesystem');
            }
            $this->fileSystem->delete($path);
        } catch (S3Exception $e) {
            throw new IliosFilesystemException('Error from AWS: ' . $e->getAwsErrorMessage());
        }
    }
}
