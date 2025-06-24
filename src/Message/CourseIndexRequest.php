<?php

declare(strict_types=1);

namespace App\Message;

use DateTime;
use InvalidArgumentException;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async_priority_low')]
class CourseIndexRequest
{
    private array $courseIds;
    private DateTime $createdAt;
    public const int MAX_COURSES = 20;

    /**
     * CourseIndexRequest constructor.
     * @param int[] $courseIds
     */
    public function __construct(array $courseIds)
    {
        $count = count($courseIds);
        if ($count > self::MAX_COURSES) {
            throw new InvalidArgumentException(
                sprintf(
                    'A maximum of %d courseIds can be indexed at the same time, you sent %d',
                    self::MAX_COURSES,
                    $count
                )
            );
        }
        $this->courseIds = $courseIds;
        $this->createdAt = new DateTime();
    }

    public function getCourseIds(): array
    {
        return $this->courseIds;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}
