<?php

declare(strict_types=1);

namespace App\Message;

use InvalidArgumentException;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async_priority_high')]
class LearningMaterialTextExtractionRequest
{
    public const int MAX_MATERIALS = 50;

    public function __construct(private readonly array $learningMaterialIds, private readonly bool $overwrite = false)
    {
        $count = count($this->learningMaterialIds);
        if ($count > self::MAX_MATERIALS) {
            throw new InvalidArgumentException(
                sprintf(
                    'A maximum of %d learning materials can be extracted at the same time, you sent %d',
                    self::MAX_MATERIALS,
                    $count
                )
            );
        }
    }

    public function getLearningMaterialIds(): array
    {
        return $this->learningMaterialIds;
    }

    public function getOverwrite(): bool
    {
        return $this->overwrite;
    }
}
