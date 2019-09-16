<?php
namespace App\EventListener;

use App\Classes\IndexableCourse;
use App\Classes\IndexableSession;
use App\Entity\CourseInterface;
use App\Entity\CourseLearningMaterialInterface;
use App\Entity\DTO\CourseDTO;
use App\Entity\DTO\UserDTO;
use App\Entity\MeshDescriptorInterface;
use App\Entity\ObjectiveInterface;
use App\Entity\SessionInterface;
use App\Entity\SessionLearningMaterialInterface;
use App\Entity\TermInterface;
use App\Entity\UserInterface;
use App\Message\CourseIndexRequest;
use App\Message\UserIndexRequest;
use App\Service\Index;
use App\Traits\IndexableCoursesEntityInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Exception;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Doctrine event listener.
 * Listen for every change to an entity and index them.
 */
class IndexEntityChanges
{
    /**
     * @var Index
     */
    private $index;

    /**
     * @var MessageBusInterface
     */
    private $bus;

    public function __construct(Index $index, MessageBusInterface $bus)
    {
        $this->index = $index;
        $this->bus = $bus;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof UserInterface) {
            $this->indexUser($entity);
        }

        if ($entity instanceof IndexableCoursesEntityInterface) {
            $this->indexCourses($entity->getIndexableCourses());
        }
    }
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof UserInterface) {
            $this->indexUser($entity);
        }

        if ($entity instanceof IndexableCoursesEntityInterface) {
            $this->indexCourses($entity->getIndexableCourses());
        }
    }

    /**
     * We have to do this work in preRemove because in postRemove we no longer
     * have access to the entity ID
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof UserInterface) {
            $this->index->deleteUser($entity->getId());
        }

        if ($entity instanceof CourseInterface) {
            $this->index->deleteCourse($entity->getId());
        }

        if ($entity instanceof SessionInterface) {
            $this->index->deleteSession($entity->getId());
            return; //don't re-index our just removed session
        }

        if ($entity instanceof IndexableCoursesEntityInterface) {
            $this->indexCourses($entity->getIndexableCourses());
        }
    }

    protected function indexUser(UserInterface $user)
    {
        if ($this->index->isEnabled()) {
            $this->bus->dispatch(new UserIndexRequest([$user->getId()]));
        }
    }

    /**
     * @param CourseInterface[] $courses
     * @throws Exception
     */
    protected function indexCourses(array $courses) : void
    {
        if ($this->index->isEnabled()) {
            $courseIds = array_map(function (CourseInterface $course) {
                return $course->getId();
            }, $courses);
            $this->bus->dispatch(new CourseIndexRequest($courseIds));
        }
    }
}
