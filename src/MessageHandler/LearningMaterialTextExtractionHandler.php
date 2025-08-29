<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\LearningMaterialIndexRequest;
use App\Message\LearningMaterialTextExtractionRequest;
use App\Repository\LearningMaterialRepository;
use App\Service\LearningMaterialTextExtractor;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Throwable;

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
        $ids = $message->getLearningMaterialIds();
        $dtos = $this->repository->findDTOsBy(['id' => $ids]);
        $overwrite = $message->getOverwrite();
        foreach ($dtos as $dto) {
            try {
                $this->extractor->extract($dto, $overwrite);
            } catch (Throwable $t) {
                if (count($ids) <= 1) {
                    throw $t;
                } else {
                    //split up the failed handling into individual requests
                    $this->bus->dispatch(new LearningMaterialTextExtractionRequest([$dto->id]));
                    $ids = array_diff($ids, [$dto->id]);
                }
            }
        }
        $chunks = array_chunk($ids, LearningMaterialIndexRequest::MAX_MATERIALS);
        foreach ($chunks as $ids) {
            $this->bus->dispatch(
                new Envelope(new LearningMaterialIndexRequest($ids, true))
                    ->with(new DispatchAfterCurrentBusStamp()),
            );
        }
    }
}
