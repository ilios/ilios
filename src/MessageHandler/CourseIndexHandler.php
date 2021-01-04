<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\CourseIndexRequest;
use App\Repository\CourseRepository;
use App\Service\Index\Curriculum;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CourseIndexHandler implements MessageHandlerInterface
{
    /**
     * @var Curriculum
     */
    private $curriculumIndex;

    /**
     * @var CourseRepository
     */
    private $courseRepository;

    public function __construct(Curriculum $curriculumIndex, CourseRepository $courseRepository)
    {
        $this->curriculumIndex = $curriculumIndex;
        $this->courseRepository = $courseRepository;
    }

    public function __invoke(CourseIndexRequest $message)
    {
        $indexes = $this->courseRepository->getCourseIndexesFor($message->getCourseIds());
        $this->curriculumIndex->index($indexes);
    }
}
