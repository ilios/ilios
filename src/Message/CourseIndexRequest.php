<?php
namespace App\Message;

class CourseIndexRequest
{
    private $courseIds;

    /**
     * CourseIndexRequest constructor.
     * @param int[] $courseIds
     */
    public function __construct(array $courseIds)
    {
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
