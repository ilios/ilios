<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Manager\CourseManager;
use App\Message\CourseIndexRequest;
use App\Service\Index\Curriculum;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CourseIndexHandler implements MessageHandlerInterface
{
    /**
     * @var Curriculum
     */
    private $curriculumIndex;

    /**
     * @var CourseManager
     */
    private $courseManager;

    public function __construct(Curriculum $curriculumIndex, CourseManager $courseManager)
    {
        $this->curriculumIndex = $curriculumIndex;
        $this->courseManager = $courseManager;
    }

    public function __invoke(CourseIndexRequest $message)
    {
        $indexes = $this->courseManager->getCourseIndexesFor($message->getCourseIds());
        $this->curriculumIndex->index($indexes);
    }
}
