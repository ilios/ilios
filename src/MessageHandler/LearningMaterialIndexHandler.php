<?php
namespace App\MessageHandler;

use App\Entity\DTO\LearningMaterialDTO;
use App\Entity\Manager\CourseManager;
use App\Entity\Manager\LearningMaterialManager;
use App\Message\LearningMaterialIndexRequest;
use App\Service\IliosFileSystem;
use App\Service\Index;
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

    public function __construct(
        Index $index,
        LearningMaterialManager $manager,
        IliosFileSystem $iliosFileSystem
    ) {
        $this->index = $index;
        $this->manager = $manager;
        $this->iliosFileSystem = $iliosFileSystem;
    }

    public function __invoke(LearningMaterialIndexRequest $message)
    {
        $dtos = $this->manager->findDTOsBy(['id' => $message->getIds()]);
        $filteredDtos = array_filter($dtos, function (LearningMaterialDTO $dto) {
            if (empty($dto->indexSessions)) {
                return false;
            }
            return $this->iliosFileSystem->checkLearningMaterialRelativePath($dto->relativePath);
        });
        if (count($filteredDtos)) {
            $this->index->indexLearningMaterials($filteredDtos);
        }
    }
}
