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

    public function __invoke(LearningMaterialIndexRequest $message)
    {
        $dtos = $this->repository->findDTOsBy(['id' => $message->getIds()]);
        $filteredDtos = array_filter(
            $dtos,
            fn(LearningMaterialDTO $dto) => $this->fileSystem->checkLearningMaterialRelativePath($dto->relativePath)
        );
        if ($filteredDtos !== []) {
            $this->learningMaterialsIndex->index($filteredDtos);
        }
    }
}
