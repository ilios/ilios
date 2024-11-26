<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\DTO\LearningMaterialDTO;
use App\Exception\LearningMaterialTextExtractorException;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Vaites\ApacheTika\Client;

class LearningMaterialTextExtractor
{
    private Client $client;

    public function __construct(
        protected NonCachingIliosFileSystem $fileSystem,
        protected TemporaryFileSystem $temporaryFileSystem,
        protected IliosFileSystem $iliosFileSystem,
        ?Client $client = null,
    ) {
        if ($client) {
            $this->client = $client;
        }
    }

    public function extract(LearningMaterialDTO $dto): void
    {
        if (!$this->isEnabled()) {
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
            throw new LearningMaterialTextExtractorException(
                "There is no material on this system at $dto->relativePath"
            );
        }

        $contents = $this->fileSystem->getFileContents($dto->relativePath);
        $tmpFile = $this->temporaryFileSystem->createFile($contents);

        try {
            $text = $this->client->getText($tmpFile->getRealPath());
            $this->iliosFileSystem->storeLearningMaterialText($dto->relativePath, $text);
        } catch (Exception $exception) {
            if (
                // error code from php-tika library (422)
                // https://github.com/vaites/php-apache-tika/blob/792dc1254b4ccd92c6964ab477a1626dca6784ee/src/Clients/WebClient.php#L620
                $exception->getCode() === Response::HTTP_UNPROCESSABLE_ENTITY &&
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

    private function isEnabled(): bool
    {
        return !empty($this->client);
    }
}
