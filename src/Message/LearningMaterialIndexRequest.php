<?php

declare(strict_types=1);

namespace App\Message;

use DateTime;
use InvalidArgumentException;

class LearningMaterialIndexRequest
{
    private DateTime $createdAt;
    public const MAX_MATERIALS = 10;

    public function __construct(private array $materialIds)
    {
        $count = count($materialIds);
        if ($count > self::MAX_MATERIALS) {
            throw new InvalidArgumentException(
                sprintf(
                    'A maximum of %d materials can be indexed at the same time, you sent %d',
                    self::MAX_MATERIALS,
                    $count
                )
            );
        }
        $this->createdAt = new DateTime();
    }

    public function getIds(): array
    {
        return $this->materialIds;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}
