<?php
namespace App\Message;

use InvalidArgumentException;

class UserIndexRequest
{
    private $userIds;
    const MAX_USERS = 250;

    /**
     * @param int[] $userIds
     */
    public function __construct(array $userIds)
    {
        $count = count($userIds);
        if ($count > self::MAX_USERS) {
            throw new InvalidArgumentException(
                sprintf(
                    'A maximum of %d userIds can be indexed at the same time, you sent %d',
                    self::MAX_USERS,
                    $count
                )
            );
        }
        $this->userIds = $userIds;
    }

    /**
     * @return int[]
     */
    public function getUserIds(): array
    {
        return $this->userIds;
    }
}
