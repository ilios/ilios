<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Manager\MeshDescriptorManager;
use App\Message\MeshDescriptorIndexRequest;
use App\Service\Index\Mesh;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class MeshDescriptorIndexHandler implements MessageHandlerInterface
{
    /**
     * @var Mesh
     */
    private $meshIndex;

    /**
     * @var MeshDescriptorManager
     */
    private $manager;

    public function __construct(Mesh $index, MeshDescriptorManager $manager)
    {
        $this->meshIndex = $index;
        $this->manager = $manager;
    }

    public function __invoke(MeshDescriptorIndexRequest $message)
    {
        $descriptors = $this->manager->getIliosMeshDescriptorsById($message->getDescriptorIds());
        $this->meshIndex->index($descriptors);
    }
}
