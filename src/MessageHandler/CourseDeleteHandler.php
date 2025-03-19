<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\CourseDeleteRequest;
use App\Service\Index\Curriculum;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CourseDeleteHandler
{
    public function __construct(
        private readonly Curriculum $curriculumIndex
    ) {
    }

    public function __invoke(CourseDeleteRequest $message): void
    {
        $this->curriculumIndex->deleteCourse($message->getCourseId());
    }
}
