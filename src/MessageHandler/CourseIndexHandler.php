<?php
namespace App\MessageHandler;

use App\Entity\Manager\CourseManager;
use App\Message\CourseIndexRequest;
use App\Service\Index;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CourseIndexHandler implements MessageHandlerInterface
{
    /**
     * @var Index
     */
    private $index;

    /**
     * @var CourseManager
     */
    private $courseManager;

    public function __construct(Index $index, CourseManager $courseManager)
    {
        $this->index = $index;
        $this->courseManager = $courseManager;
    }

    public function __invoke(CourseIndexRequest $message)
    {
        $indexes = $this->courseManager->getCourseIndexesFor($message->getCourseIds());
        $this->index->indexCourses($indexes);
    }
}
