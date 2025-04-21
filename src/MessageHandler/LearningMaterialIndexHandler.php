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
    ) {
    }

    public function __invoke(LearningMaterialIndexRequest $message): void
    {
        $dtos = $this->repository->findDTOsBy(['id' => $message->getIds()]);
        $this->learningMaterialsIndex->index($dtos);
    }
}
