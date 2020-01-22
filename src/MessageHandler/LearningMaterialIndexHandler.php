<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\DTO\LearningMaterialDTO;
use App\Entity\Manager\LearningMaterialManager;
use App\Message\LearningMaterialIndexRequest;
use App\Service\Index\LearningMaterials;
use App\Service\NonCachingIliosFileSystem;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class LearningMaterialIndexHandler implements MessageHandlerInterface
{
    /**
     * @var LearningMaterials
     */
    private $learningMaterialsIndex;

    /**
     * @var LearningMaterialManager
     */
    private $manager;

    /**
     * @var NonCachingIliosFileSystem
     */
    private $fileSystem;

    public function __construct(
        LearningMaterials $index,
        LearningMaterialManager $manager,
        NonCachingIliosFileSystem $fileSystem
    ) {
        $this->learningMaterialsIndex = $index;
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
            $this->learningMaterialsIndex->index($filteredDtos);
        }
    }
}
