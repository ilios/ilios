<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\LocalCachingFilesystemDecorator;
use App\Exception\IliosFilesystemException;
use Aws\S3\Exception\S3Exception;
use Exception;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;
use Symfony\Component\HttpFoundation\File\File;
use App\Entity\LearningMaterialInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class IliosFileSystem
 *
 */
class IliosFileSystem
{
    /**
     * New learning materials whose path is based on their
     * file hash are stored in this subdirectory of the
     * learning_material directory
     */
    public const string HASHED_LM_DIRECTORY = 'learning_materials/lm';

    /**
     * Extracted test from uploaded materials are stored here
     * based on the hashed file name of the LM they were
     * extracted from
     */
    public const string HASHED_LM_TEXT_DIRECTORY = 'learning_materials/text';

    /**
     * Lock files are stored in this directory
     */
    public const string LOCK_FILE_DIRECTORY = 'locks';

    /**
     * Temporary File which need to be shared across servers
     */
    public const string TEMPORARY_SHARED_FILE_DIRECTORY = 'tmp';

    /**
     * Testing files are stored in this directory
     */
    public const string TEST_FILE_ROOT = 'crud_tests';

    public function __construct(protected FilesystemOperator $fileSystem)
    {
    }

    /**
     * Store a learning material file and return the relativePath
     */
    public function storeLearningMaterialFile(File $file): string
    {
        $relativePath = $this->getLearningMaterialFilePath($file);
        $stream = fopen($file->getPathname(), 'r+');
        $this->fileSystem->writeStream($relativePath, $stream);
        fclose($stream);

        return $relativePath;
    }

    /**
     * Get the path to a learning material file
     */
    public function getLearningMaterialFilePath(File $file): string
    {
        $hash = md5_file($file->getPathname());

        return self::HASHED_LM_DIRECTORY . '/' . substr($hash, 0, 2) . '/' . $hash;
    }

    /**
     * Store the extracted text from a learning material
     */
    public function storeLearningMaterialText(string $lmRelativePath, string $contents): string
    {
        $relativePath = $this->getLearningMaterialTextPath($lmRelativePath);
        $this->fileSystem->write($relativePath, $contents);

        return $relativePath;
    }

    /**
     * Get the path to a learning material extracted text file
     */
    public function getLearningMaterialTextPath(string $lmRelativePath): string
    {
        $arr = explode('/', $lmRelativePath);
        $hash = $arr[3];
        return self::HASHED_LM_TEXT_DIRECTORY . '/' . substr($hash, 0, 2) . '/' . $hash . '.txt';
    }

    /**
     * Remove a file from the filesystem by hash
     */
    public function removeFile(string $relativePath): void
    {
        $this->fileSystem->delete($relativePath);
    }


    /**
     * Get a File from a hash
     */
    public function getFileContents(string $relativePath): string|false
    {
        if ($this->fileSystem->fileExists($relativePath)) {
            return $this->fileSystem->read($relativePath);
        }

        return false;
    }

    /**
     * Get if a learning material has a valid file path
     */
    public function checkLearningMaterialFilePath(LearningMaterialInterface $lm): bool
    {
        $relativePath = $lm->getRelativePath();
        return $this->checkLearningMaterialRelativePath($relativePath);
    }

    /**
     * Get if a learning material file path is valid
     */
    public function checkLearningMaterialRelativePath(string $path): bool
    {
        return $this->fileSystem->fileExists($path);
    }

    /**
     * Get the path for a lock file
     */
    protected function getLockFilePath(string $name): string
    {
        $safeName = preg_replace('/[^a-z0-9\._-]+/i', '-', $name);
        return self::LOCK_FILE_DIRECTORY . '/' . $safeName;
    }

    /**
     * Create a lock file
     */
    public function createLock(string $name): void
    {
        if ($this->hasLock($name)) {
            return;
        }
        $relativePath = $this->getLockFilePath($name);
        if (!$this->fileSystem->fileExists($relativePath)) {
            $this->fileSystem->write($relativePath, 'LOCK');
        }
    }

    /**
     * Remove a lock file
     */
    public function releaseLock(string $name): void
    {
        if (!$this->hasLock($name)) {
            return;
        }
        $relativePath = $this->getLockFilePath($name);
        if ($this->fileSystem->fileExists($relativePath)) {
            $this->fileSystem->delete($relativePath);
        }
    }

    /**
     * Check if a lock file exists
     */
    public function hasLock(string $name): bool
    {
        $relativePath = $this->getLockFilePath($name);

        return $this->fileSystem->fileExists($relativePath);
    }

    /**
     * Wait for and then acquire a lock
     */
    public function waitForLock(string $name): void
    {
        while ($this->hasLock($name)) {
            usleep(250);
        }
        $this->createLock($name);
    }

    /**
     * Test Create, Read, Update, Delete on our filesystem
     */
    public function testCRUD(): void
    {
        $path = self::TEST_FILE_ROOT . '/test-file-' . uniqid();
        $contents = md5_file(__FILE__);

        try {
            //cleanup any existing test files
            $this->fileSystem->deleteDirectory(self::TEST_FILE_ROOT);
        } catch (UnableToDeleteDirectory) {
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
            try {
                $this->fileSystem->write($path, $contents . $contents);
                $result = $this->fileSystem->read($path);
                if ($result !==  $contents . $contents) {
                    throw new IliosFilesystemException('Unable to Update Filesystem');
                }
                $this->fileSystem->delete($path);
            } catch (FilesystemException | UnableToWriteFile) {
                throw new IliosFilesystemException('Unable to Update Filesystem');
            }
        } catch (S3Exception $e) {
            throw new IliosFilesystemException('Error from AWS: ' . $e->getAwsErrorMessage());
        }
    }

    protected function getTemporaryFilePath(string $hash): string
    {
        return self::TEMPORARY_SHARED_FILE_DIRECTORY . '/' . $hash;
    }

    /**
     * Store an uploaded file and return the hash
     */
    public function storeUploadedTemporaryFile(UploadedFile $file): string
    {
        $hash = md5_file($file->getPathname());
        $relativePath = $this->getTemporaryFilePath($hash);
        $stream = fopen($file->getPathname(), 'r+');
        $this->fileSystem->writeStream($relativePath, $stream);
        fclose($stream);

        return $hash;
    }

    public function getUploadedTemporaryFileContentsAndRemoveFile(string $hash): ?string
    {
        $relativePath = $this->getTemporaryFilePath($hash);
        if ($this->fileSystem->fileExists($relativePath)) {
            try {
                $result = $this->fileSystem->read($relativePath);
                $this->fileSystem->delete($relativePath);
                return $result;
            } catch (UnableToReadFile) {
                throw new Exception("Unable to read temporary file {$hash}");
            } catch (UnableToDeleteFile) {
                throw new Exception("Unable to delete temporary file {$hash}");
            }
        }

        return null;
    }
}
