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
     * @var Curriculum
     */
    protected $curriculumIndex;

    /**
     * @var LearningMaterials
     */
    protected $learningMaterialsIndex;

    /**
     * @var Mesh
     */
    protected $meshIndex;

    /**
     * @var Users
     */
    protected $usersIndex;

    /**
     * @var MessageBusInterface
     */
    protected $bus;

    /**
     * ACHTUNG!!! Do NOT change the name of $dispatchBus it tells the dependency injection system what bus to inject!!!
     */
    public function __construct(
        Curriculum $curriculumIndex,
        LearningMaterials $learningMaterialsIndex,
        Mesh $meshIndex,
        Users $usersIndex,
        MessageBusInterface $dispatchBus
    ) {
        $this->bus = $dispatchBus;
        $this->curriculumIndex = $curriculumIndex;
        $this->meshIndex = $meshIndex;
        $this->usersIndex = $usersIndex;
        $this->learningMaterialsIndex = $learningMaterialsIndex;
    }

    public function postPersist(LifecycleEventArgs $args)
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
    public function postUpdate(LifecycleEventArgs $args)
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
    public function preRemove(LifecycleEventArgs $args)
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

    protected function indexUser(UserInterface $user)
    {
        if ($this->usersIndex->isEnabled()) {
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
            $courseIds = array_map(function (CourseInterface $course) {
                return $course->getId();
            }, $courses);
            $chunks = array_chunk($courseIds, CourseIndexRequest::MAX_COURSES);
            foreach ($chunks as $ids) {
                $this->bus->dispatch(new CourseIndexRequest($ids));
            }
        }
    }

    protected function indexLearningMaterial(LearningMaterialInterface $lm)
    {
//        temporarily disable indexing learning materials while we figure out performance
//        if ($this->learningMaterialsIndex->isEnabled()) {
//            $this->bus->dispatch(new LearningMaterialIndexRequest($lm->getId()));
//        }
    }
}
