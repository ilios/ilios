<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\LearningMaterialDeleteRequest;
use App\Service\Index\LearningMaterials;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class LearningMaterialDeleteHandler
{
    public function __construct(
        private readonly LearningMaterials $learningMaterialIndex,
    ) {
    }

    public function __invoke(LearningMaterialDeleteRequest $message): void
    {
        $this->learningMaterialIndex->delete($message->getLearningMaterialId());
    }
}
