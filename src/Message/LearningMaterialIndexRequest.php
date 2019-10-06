<?php
namespace App\Message;

use InvalidArgumentException;

class LearningMaterialIndexRequest
{
    private $ids;
    const MAX = 10;

    /**
     * LearningMaterialIndexRequest constructor.
     * @param int[] $ids
     */
    public function __construct(array $ids)
    {
        $count = count($ids);
        if ($count > self::MAX) {
            throw new InvalidArgumentException(
                sprintf(
                    'A maximum of %d learning material ids can be indexed at the same time, you sent %d',
                    self::MAX,
                    $count
                )
            );
        }
        $this->ids = $ids;
    }

    /**
     * @return int[]
     */
    public function getIds(): array
    {
        return $this->ids;
    }
}
