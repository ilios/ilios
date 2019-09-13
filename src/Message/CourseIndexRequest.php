<?php
namespace App\Message;

use InvalidArgumentException;

class CourseIndexRequest
{
    private $courseIds;
    const MAX_COURSES = 50;

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
    }

    /**
     * @return int[]
     */
    public function getCourseIds(): array
    {
        return $this->courseIds;
    }
}
