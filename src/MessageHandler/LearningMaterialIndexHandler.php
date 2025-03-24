<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\DTO\LearningMaterialDTO;
use App\Message\LearningMaterialIndexRequest;
use App\Repository\LearningMaterialRepository;
use App\Service\Index\LearningMaterials;
use App\Service\NonCachingIliosFileSystem;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class LearningMaterialIndexHandler
{
    public function __construct(
        private LearningMaterials $learningMaterialsIndex,
        private LearningMaterialRepository $repository,
        private NonCachingIliosFileSystem $fileSystem
    ) {
    }

    public function __invoke(LearningMaterialIndexRequest $message): void
    {
        $dtos = $this->repository->findDTOsBy(['id' => $message->getIds()]);
        $materialsWithText = array_filter(
            $dtos,
            fn(LearningMaterialDTO $dto) => $this->fileSystem->checkIfLearningMaterialTextFileExists($dto->relativePath)
        );
        if ($materialsWithText !== []) {
            $this->learningMaterialsIndex->index($materialsWithText);
        }
    }
}
