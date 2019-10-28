<?php
namespace App\MessageHandler;

use App\Entity\DTO\LearningMaterialDTO;
use App\Entity\Manager\LearningMaterialManager;
use App\Message\LearningMaterialIndexRequest;
use App\Service\IliosFileSystem;
use App\Service\Index;
use Psr\Log\LoggerInterface;
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
     * @var IliosFileSystem
     */
    private $iliosFileSystem;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Index $index,
        LearningMaterialManager $manager,
        IliosFileSystem $iliosFileSystem,
        LoggerInterface $logger
    ) {
        $this->index = $index;
        $this->manager = $manager;
        $this->iliosFileSystem = $iliosFileSystem;
        $this->logger = $logger;
    }

    public function __invoke(LearningMaterialIndexRequest $message)
    {
        $dtos = $this->manager->findDTOsBy(['id' => $message->getId()]);
        $filteredDtos = array_filter($dtos, function (LearningMaterialDTO $dto) {
            return $this->iliosFileSystem->checkLearningMaterialRelativePath($dto->relativePath);
        });
        if (count($filteredDtos)) {
            $this->logger->debug('Start Indexing Learning Materials', [
                'material_ids' => array_column($filteredDtos, 'id'),
            ]);
            $this->index->indexLearningMaterials($filteredDtos);
            $this->logger->debug('Complete Indexing Learning Materials', [
                'material_ids' => array_column($filteredDtos, 'id'),
            ]);
        }
    }
}
