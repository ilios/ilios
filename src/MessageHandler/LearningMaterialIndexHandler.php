<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\DTO\LearningMaterialDTO;
use App\Message\LearningMaterialIndexRequest;
use App\Repository\LearningMaterialRepository;
use App\Service\Index\LearningMaterials;
use App\Service\NonCachingIliosFileSystem;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class LearningMaterialIndexHandler implements MessageHandlerInterface
{
    /**
     * @var LearningMaterials
     */
    private $learningMaterialsIndex;

    private LearningMaterialRepository $repository;

    /**
     * @var NonCachingIliosFileSystem
     */
    private $fileSystem;

    public function __construct(
        LearningMaterials $index,
        LearningMaterialRepository $repository,
        NonCachingIliosFileSystem $fileSystem
    ) {
        $this->learningMaterialsIndex = $index;
        $this->repository = $repository;
        $this->fileSystem = $fileSystem;
    }

    public function __invoke(LearningMaterialIndexRequest $message)
    {
        $dtos = $this->repository->findDTOsBy(['id' => $message->getId()]);
        $filteredDtos = array_filter(
            $dtos,
            fn(LearningMaterialDTO $dto) => $this->fileSystem->checkLearningMaterialRelativePath($dto->relativePath)
        );
        if ($filteredDtos !== []) {
            $this->learningMaterialsIndex->index($filteredDtos);
        }
    }
}
