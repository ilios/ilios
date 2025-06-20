<?php

declare(strict_types=1);

namespace App\Message;

use InvalidArgumentException;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async_priority_normal')]
class MeshDescriptorIndexRequest
{
    private array $descriptorIds;
    public const int MAX_DESCRIPTORS = 250;

    /**
     * @param int[] $descriptorIds
     */
    public function __construct(array $descriptorIds)
    {
        $count = count($descriptorIds);
        if ($count > self::MAX_DESCRIPTORS) {
            throw new InvalidArgumentException(
                sprintf(
                    'A maximum of %d descriptorIds can be indexed at the same time, you sent %d',
                    self::MAX_DESCRIPTORS,
                    $count
                )
            );
        }
        $this->descriptorIds = $descriptorIds;
    }

    public function getDescriptorIds(): array
    {
        return $this->descriptorIds;
    }
}
