<?php
namespace App\Message;

use InvalidArgumentException;

class MeshDescriptorIndexRequest
{
    private $descriptorIds;
    const MAX_DESCRIPTORS = 250;

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

    /**
     * @return int[]
     */
    public function getDescriptorIds(): array
    {
        return $this->descriptorIds;
    }
}
