<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\MeshDescriptorIndexRequest;
use App\Repository\MeshDescriptorRepository;
use App\Service\Index\Mesh;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class MeshDescriptorIndexHandler implements MessageHandlerInterface
{
    /**
     * @var Mesh
     */
    private $meshIndex;
    private MeshDescriptorRepository $repository;

    public function __construct(Mesh $index, MeshDescriptorRepository $repository)
    {
        $this->meshIndex = $index;
        $this->repository = $repository;
    }

    public function __invoke(MeshDescriptorIndexRequest $message)
    {
        $descriptors = $this->repository->getIliosMeshDescriptorsById($message->getDescriptorIds());
        $this->meshIndex->index($descriptors);
    }
}
