<?php

declare(strict_types=1);

namespace App\Message;

use InvalidArgumentException;

class LearningMaterialIndexRequest
{
    private array $ids;
    public const int MAX_MATERIALS = 10;

    public function __construct(array $ids, protected bool $force = false)
    {
        $count = count($ids);
        if ($count > self::MAX_MATERIALS) {
            throw new InvalidArgumentException(
                sprintf(
                    'A maximum of %d materials can be indexed at the same time, you sent %d',
                    self::MAX_MATERIALS,
                    $count
                )
            );
        }
        $this->ids = $ids;
    }

    public function getIds(): array
    {
        return $this->ids;
    }

    public function getForce(): bool
    {
        return $this->force;
    }
}
