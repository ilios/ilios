<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
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
