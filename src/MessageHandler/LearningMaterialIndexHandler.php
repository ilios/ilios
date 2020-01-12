<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\DTO\LearningMaterialDTO;
use App\Entity\Manager\LearningMaterialManager;
use App\Message\LearningMaterialIndexRequest;
use App\Service\Index;
use App\Service\NonCachingIliosFileSystem;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class LearningMaterialIndexHandler implements MessageHandlerInterface
{
    /**
     * @var Index
     */
    private $index;

    /**
     * @var LearningMaterialManager
     */
    private $manager;

    /**
     * @var NonCachingIliosFileSystem
     */
    private $fileSystem;

    public function __construct(
        Index $index,
        LearningMaterialManager $manager,
        NonCachingIliosFileSystem $fileSystem
    ) {
        $this->index = $index;
        $this->manager = $manager;
        $this->fileSystem = $fileSystem;
    }

    public function __invoke(LearningMaterialIndexRequest $message)
    {
        $dtos = $this->manager->findDTOsBy(['id' => $message->getId()]);
        $filteredDtos = array_filter($dtos, function (LearningMaterialDTO $dto) {
            return $this->fileSystem->checkLearningMaterialRelativePath($dto->relativePath);
        });
        if (count($filteredDtos)) {
            $this->index->indexLearningMaterials($filteredDtos);
        }
    }
}
