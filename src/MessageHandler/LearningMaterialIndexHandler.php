<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\LearningMaterialIndexRequest;
use App\Repository\LearningMaterialRepository;
use App\Service\Config;
use App\Service\Index\LearningMaterials;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class LearningMaterialIndexHandler
{
    public function __construct(
        private readonly LearningMaterials $learningMaterialsIndex,
        private readonly LearningMaterialRepository $repository,
        private readonly Config $config,
    ) {
    }

    public function __invoke(LearningMaterialIndexRequest $message): void
    {
        if (!$this->config->get('learningMaterialsDisabled')) {
            $dtos = $this->repository->findDTOsBy(['id' => $message->getIds()]);
            $this->learningMaterialsIndex->index($dtos, $message->getForce());
        }
    }
}
