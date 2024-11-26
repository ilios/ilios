<?php

declare(strict_types=1);

namespace App\Message;

use InvalidArgumentException;

class LearningMaterialTextExtractionRequest
{
    private array $learningMaterialIds;
    public const int MAX_MATERIALS = 50;

    public function __construct(array $learningMaterialIds)
    {
        $count = count($learningMaterialIds);
        if ($count > self::MAX_MATERIALS) {
            throw new InvalidArgumentException(
                sprintf(
                    'A maximum of %d learning materials can be extracted at the same time, you sent %d',
                    self::MAX_MATERIALS,
                    $count
                )
            );
        }
        $this->learningMaterialIds = $learningMaterialIds;
    }

    public function getLearningMaterialIds(): array
    {
        return $this->learningMaterialIds;
    }
}
