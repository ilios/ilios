<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\MeshDescriptorIndexRequest;
use App\Repository\MeshDescriptorRepository;
use App\Service\Index\Mesh;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class MeshDescriptorIndexHandler
{
    public function __construct(private Mesh $meshIndex, private MeshDescriptorRepository $repository)
    {
    }

    public function __invoke(MeshDescriptorIndexRequest $message): void
    {
        $descriptors = $this->repository->getIliosMeshDescriptorsById($message->getDescriptorIds());
        $this->meshIndex->index($descriptors);
    }
}
