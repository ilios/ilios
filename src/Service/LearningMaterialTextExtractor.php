<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\DTO\LearningMaterialDTO;
use Exception;
use Vaites\ApacheTika\Client;

class LearningMaterialTextExtractor
{
    private bool $enabled = false;
    private Client $client;

    public function __construct(
        protected NonCachingIliosFileSystem $fileSystem,
        protected TemporaryFileSystem $temporaryFileSystem,
        protected IliosFileSystem $iliosFileSystem,
        ?Client $client = null,
    ) {
        if ($client) {
            $this->enabled = true;
            $this->client = $client;
        }
    }

    public function extract(LearningMaterialDTO $dto): void
    {
        if (!$this->enabled) {
            return;
        }

        if (!$dto->filename) {
            //not a File LM
            return;
        }

        if (!$this->client->isMIMETypeSupported($dto->mimetype)) {
            //not the type of file tika can extract
            return;
        }

        if ($this->iliosFileSystem->checkIfLearningMaterialTextFileExists($dto->relativePath)) {
            //this LM has already been extracted
            return;
        }

        if (!$this->fileSystem->checkLearningMaterialRelativePath($dto->relativePath)) {
            throw new Exception("There is no material on this system at $dto->relativePath");
        }

        $contents = $this->fileSystem->getFileContents($dto->relativePath);
        $tmpFile = $this->temporaryFileSystem->createFile($contents);

        try {
            $text = $this->client->getText($tmpFile->getRealPath());
            $this->iliosFileSystem->storeLearningMaterialText($dto->relativePath, $text);
        } catch (Exception $exception) {
            if (
                $exception->getCode() === 422 &&
                $exception->getMessage() === 'Unprocessable document'
            ) {
                //this document can't be processed by tika
                //we don't want to keep trying, so we store the filename as the text
                $this->iliosFileSystem->storeLearningMaterialText($dto->relativePath, $dto->filename);
            }
        } finally {
            if (file_exists($tmpFile->getRealPath())) {
                unlink($tmpFile->getRealPath());
            }
        }
    }
}
