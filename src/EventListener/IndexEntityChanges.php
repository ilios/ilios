<?php
namespace App\EventListener;

use App\Entity\CourseInterface;
use App\Entity\DTO\CourseDTO;
use App\Entity\DTO\UserDTO;
use App\Entity\UserInterface;
use App\Service\Index;
use Doctrine\ORM\Event\LifecycleEventArgs;

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

    public function __construct(Index $index)
    {
        $this->index = $index;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof UserInterface) {
            $this->indexUser($entity);
        }
        if ($entity instanceof CourseInterface) {
            $this->indexCourse($entity);
        }
    }
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof UserInterface) {
            $this->indexUser($entity);
        }
        if ($entity instanceof CourseInterface) {
            $this->indexCourse($entity);
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
    }

    protected function indexUser(UserInterface $user)
    {
        if ($this->index->isEnabled()) {
            $dto = UserDTO::createSearchIndexDTOFromEntity($user);
            $this->index->indexUsers([$dto]);
        }
    }

    protected function indexCourse(CourseInterface $course)
    {
        if ($this->index->isEnabled()) {
            $dto = CourseDTO::createSearchIndexDTOFromEntity($course);
             $this->index->indexCourses([$dto]);
        }
    }
}
