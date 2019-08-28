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
use App\Service\Index;
use App\Traits\IndexableCoursesEntityInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Exception;

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
            $dto = UserDTO::createSearchIndexDTOFromEntity($user);
            $this->index->indexUsers([$dto]);
        }
    }

    /**
     * @param CourseInterface[] $courses
     * @throws Exception
     */
    protected function indexCourses(array $courses) : void
    {
        if ($this->index->isEnabled()) {
            $indexes = array_map([$this, 'getIndexableCourse'], $courses);
            $this->index->indexCourses($indexes);
        }
    }

    protected function getIndexableCourse(CourseInterface $course) : IndexableCourse
    {
        $index = new IndexableCourse();
        $dto = new CourseDTO(
            $course->getId(),
            $course->getTitle(),
            $course->getLevel(),
            $course->getYear(),
            $course->getStartDate(),
            $course->getEndDate(),
            $course->getExternalId(),
            $course->isLocked(),
            $course->isArchived(),
            $course->isPublishedAsTbd(),
            $course->isPublished()
        );

        $index->courseDTO = $dto;
        $index->school = $course->getSchool()->getTitle();
        if ($clerkshipType = $course->getClerkshipType()) {
            $index->clerkshipType = $clerkshipType->getTitle();
        }
        $index->directors = array_map(function (UserInterface $user) {
            return $user->getFirstAndLastName() . ' ' . $user->getDisplayName();
        }, $course->getDirectors()->toArray());
        $index->administrators = array_map(function (UserInterface $user) {
            return $user->getFirstAndLastName() . ' ' . $user->getDisplayName();
        }, $course->getAdministrators()->toArray());
        $index->terms = array_map(function (TermInterface $term) {
            return $term->getTitle();
        }, $course->getTerms()->toArray());
        $index->objectives = array_map(function (ObjectiveInterface $objective) {
            return $objective->getTitle();
        }, $course->getObjectives()->toArray());
        $index->meshDescriptorIds = array_map(function (MeshDescriptorInterface $descriptor) {
            return $descriptor->getId();
        }, $course->getMeshDescriptors()->toArray());
        $index->meshDescriptorNames = array_map(function (MeshDescriptorInterface $descriptor) {
            return $descriptor->getName();
        }, $course->getMeshDescriptors()->toArray());
        $index->meshDescriptorAnnotations = array_map(function (MeshDescriptorInterface $descriptor) {
            return $descriptor->getAnnotation();
        }, $course->getMeshDescriptors()->toArray());
        $index->learningMaterials = array_map(function (CourseLearningMaterialInterface $clm) {
            $lm = $clm->getLearningMaterial();
            return $lm->getTitle() . ' ' . $lm->getDescription();
        }, $course->getLearningMaterials()->toArray());
        foreach ($course->getSessions() as $session) {
            $sessionIndex = new IndexableSession();
            $sessionIndex->sessionId = $session->getId();
            $sessionIndex->title = $session->getTitle();
            if ($sessionDescription = $session->getSessionDescription()) {
                $sessionIndex->description = $sessionDescription->getDescription();
            }
            $sessionIndex->sessionType = $session->getSessionType()->getTitle();

            $sessionIndex->administrators = array_map(function (UserInterface $user) {
                return $user->getFirstAndLastName() . ' ' . $user->getDisplayName();
            }, $session->getAdministrators()->toArray());
            $sessionIndex->terms = array_map(function (TermInterface $term) {
                return $term->getTitle();
            }, $session->getTerms()->toArray());
            $sessionIndex->objectives = array_map(function (ObjectiveInterface $objective) {
                return $objective->getTitle();
            }, $session->getObjectives()->toArray());
            $sessionIndex->meshDescriptorIds = array_map(function (MeshDescriptorInterface $descriptor) {
                return $descriptor->getId();
            }, $session->getMeshDescriptors()->toArray());
            $sessionIndex->meshDescriptorNames = array_map(function (MeshDescriptorInterface $descriptor) {
                return $descriptor->getName();
            }, $session->getMeshDescriptors()->toArray());
            $sessionIndex->meshDescriptorAnnotations = array_map(function (MeshDescriptorInterface $descriptor) {
                return $descriptor->getAnnotation();
            }, $session->getMeshDescriptors()->toArray());
            $sessionIndex->learningMaterials = array_map(function (SessionLearningMaterialInterface $slm) {
                $lm = $slm->getLearningMaterial();
                return $lm->getTitle() . ' ' . $lm->getDescription();
            }, $session->getLearningMaterials()->toArray());

            $index->sessions[] = $sessionIndex;
        }
        return $index;
    }
}
