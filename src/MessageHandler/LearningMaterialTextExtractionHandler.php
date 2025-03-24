<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\LearningMaterialIndexRequest;
use App\Message\LearningMaterialTextExtractionRequest;
use App\Repository\LearningMaterialRepository;
use App\Service\LearningMaterialTextExtractor;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class LearningMaterialTextExtractionHandler
{
    public function __construct(
        protected LearningMaterialTextExtractor $extractor,
        protected LearningMaterialRepository $repository,
        protected MessageBusInterface $bus,
    ) {
    }

    public function __invoke(LearningMaterialTextExtractionRequest $message): void
    {
        $dtos = $this->repository->findDTOsBy(['id' => $message->getLearningMaterialIds()]);
        foreach ($dtos as $dto) {
            $this->extractor->extract($dto);
        }
        $this->bus->dispatch(new LearningMaterialIndexRequest([$message->getLearningMaterialIds()]));
    }
}
