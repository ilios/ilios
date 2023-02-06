<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\CourseIndexRequest;
use App\Repository\CourseRepository;
use App\Service\Index\Curriculum;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CourseIndexHandler implements MessageHandlerInterface
{
    public function __construct(
        private Curriculum $curriculumIndex,
        private CourseRepository $courseRepository
    ) {
    }

    public function __invoke(CourseIndexRequest $message)
    {
        $indexes = $this->courseRepository->getCourseIndexesFor($message->getCourseIds());
        $this->curriculumIndex->index($indexes, $message->getCreatedAt());
    }
}
