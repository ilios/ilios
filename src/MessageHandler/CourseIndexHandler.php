<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\CourseIndexRequest;
use App\Service\Index\Curriculum;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

#[AsMessageHandler]
class CourseIndexHandler
{
    public function __construct(
        private readonly Curriculum $curriculumIndex,
        private readonly MessageBusInterface $bus,
    ) {
    }

    public function __invoke(CourseIndexRequest $message): void
    {
        try {
            $this->curriculumIndex->index($message->getCourseIds(), $message->getCreatedAt());
        } catch (Throwable $t) {
            if (count($message->getCourseIds()) <= 1) {
                throw $t;
            } else {
                //split up the failed handling into individual requests
                foreach ($message->getCourseIds() as $courseId) {
                    $this->bus->dispatch(new CourseIndexRequest([$courseId]));
                }
            }
        }
    }
}
