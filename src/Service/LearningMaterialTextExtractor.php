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

        if (!$this->fileSystem->checkLearningMaterialRelativePath($dto->relativePath)) {
            throw new Exception("There is no material on this system at $dto->relativePath");
        }

        $contents = $this->fileSystem->getFileContents($dto->relativePath);
        $tmpFile = $this->temporaryFileSystem->createFile($contents);
        $text = $this->client->getText($tmpFile->getRealPath());
        $this->iliosFileSystem->storeLearningMaterialText($dto->relativePath, $text);
        unlink($tmpFile->getRealPath());
    }
}
