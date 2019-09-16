<?php
namespace App\MessageHandler;

use App\Entity\Manager\MeshDescriptorManager;
use App\Message\MeshDescriptorIndexRequest;
use App\Service\Index;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class MeshDescriptorIndexHandler implements MessageHandlerInterface
{
    /**
     * @var Index
     */
    private $index;

    /**
     * @var MeshDescriptorManager
     */
    private $manager;

    public function __construct(Index $index, MeshDescriptorManager $manager)
    {
        $this->index = $index;
        $this->manager = $manager;
    }

    public function __invoke(MeshDescriptorIndexRequest $message)
    {
        $descriptors = $this->manager->getIliosMeshDescriptorsById($message->getDescriptorIds());
        $this->index->indexMeshDescriptors($descriptors);
    }
}
