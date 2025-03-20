<?php

declare(strict_types=1);

namespace App\Message;

class CourseDeleteRequest
{
    public function __construct(private readonly int $courseId)
    {
    }

    public function getCourseId(): int
    {
        return $this->courseId;
    }
}
