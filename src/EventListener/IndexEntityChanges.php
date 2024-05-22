<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\AuthenticationInterface;
use App\Entity\CourseInterface;
use App\Entity\LearningMaterialInterface;
use App\Entity\SessionInterface;
use App\Entity\UserInterface;
use App\Message\CourseIndexRequest;
use App\Message\LearningMaterialIndexRequest;
use App\Message\UserIndexRequest;
use App\Service\Index\Curriculum;
use App\Service\Index\LearningMaterials;
use App\Service\Index\Mesh;
use App\Service\Index\Users;
use App\Traits\IndexableCoursesEntityInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Doctrine event listener.
 * Listen for every change to an entity and index them.
 */
class IndexEntityChanges
{
    /**
     * ACHTUNG!!! Do NOT change the name of $dispatchBus it tells the dependency injection system what bus to inject!!!
     */
    public function __construct(
        protected Curriculum $curriculumIndex,
        protected LearningMaterials $learningMaterialsIndex,
        protected Mesh $meshIndex,
        protected Users $usersIndex,
        protected MessageBusInterface $bus,
        protected LoggerInterface $logger,
    ) {
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof UserInterface) {
            $this->indexUser($entity);
        }

        if ($entity instanceof AuthenticationInterface) {
            $this->indexUser($entity->getUser());
        }

        if ($entity instanceof LearningMaterialInterface) {
            $this->indexLearningMaterial($entity);
        }

        if ($entity instanceof IndexableCoursesEntityInterface) {
            $this->indexCourses($entity->getIndexableCourses());
        }
    }
    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof UserInterface) {
            $this->indexUser($entity);
        }

        if ($entity instanceof AuthenticationInterface) {
            $this->indexUser($entity->getUser());
        }

        if ($entity instanceof LearningMaterialInterface) {
            $this->indexLearningMaterial($entity);
        }

        if ($entity instanceof IndexableCoursesEntityInterface) {
            $this->indexCourses($entity->getIndexableCourses());
        }
    }

    /**
     * We have to do this work in preRemove because in postRemove we no longer
     * have access to the entity ID
     */
    public function preRemove(PreRemoveEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof UserInterface) {
            $this->usersIndex->delete($entity->getId());
        }

        if ($entity instanceof AuthenticationInterface) {
            $this->indexUser($entity->getUser());
        }

        if ($entity instanceof CourseInterface) {
            $this->curriculumIndex->deleteCourse($entity->getId());
        }

        if ($entity instanceof SessionInterface) {
            $this->curriculumIndex->deleteSession($entity->getId());
            return; //don't re-index our just removed session
        }

        if ($entity instanceof LearningMaterialInterface) {
            $this->learningMaterialsIndex->delete($entity->getId());
        }

        if ($entity instanceof IndexableCoursesEntityInterface) {
            $this->indexCourses($entity->getIndexableCourses());
        }
    }

    protected function indexUser(UserInterface $user): void
    {
        if ($this->usersIndex->isEnabled()) {
            $this->logger->log('debug', 'Indexing User ' . $user->getId());
            $this->bus->dispatch(new UserIndexRequest([$user->getId()]));
        }
    }

    /**
     * @param CourseInterface[] $courses
     * @throws Exception
     */
    protected function indexCourses(array $courses): void
    {
        if ($this->curriculumIndex->isEnabled()) {
            $courseIds = array_map(fn(CourseInterface $course) => $course->getId(), $courses);
            $chunks = array_chunk($courseIds, CourseIndexRequest::MAX_COURSES);
            foreach ($chunks as $ids) {
                $this->logger->log('debug', 'Indexing Courses [' . implode(', ', $ids) . ']');
                $this->bus->dispatch(new CourseIndexRequest($ids));
            }
        }
    }

    protected function indexLearningMaterial(LearningMaterialInterface $lm)
    {
        if ($this->learningMaterialsIndex->isEnabled()) {
            $this->logger->log('debug', 'Indexing Material ' . $lm->getId());
            $this->bus->dispatch(new LearningMaterialIndexRequest($lm->getId()));
        }
    }
}
