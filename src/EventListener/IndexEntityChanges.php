<?php
namespace App\EventListener;

use App\Classes\IndexableCourse;
use App\Entity\CourseInterface;
use App\Entity\CourseLearningMaterialInterface;
use App\Entity\DTO\CourseDTO;
use App\Entity\DTO\UserDTO;
use App\Entity\MeshDescriptorInterface;
use App\Entity\ObjectiveInterface;
use App\Entity\SessionLearningMaterialInterface;
use App\Entity\TermInterface;
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
            $index->courseDirectors = array_map(function (UserInterface $user) {
                return $user->getFirstAndLastName() . ' ' . $user->getDisplayName();
            }, $course->getDirectors()->toArray());
            $index->courseAdministrators = array_map(function (UserInterface $user) {
                return $user->getFirstAndLastName() . ' ' . $user->getDisplayName();
            }, $course->getAdministrators()->toArray());
            $index->courseTerms = array_map(function (TermInterface $term) {
                return $term->getTitle();
            }, $course->getTerms()->toArray());
            $index->courseObjectives = array_map(function (ObjectiveInterface $objective) {
                return $objective->getTitle();
            }, $course->getObjectives()->toArray());
            $index->courseMeshDescriptors = array_map(function (MeshDescriptorInterface $descriptor) {
                return $descriptor->getId();
            }, $course->getMeshDescriptors()->toArray());
            $index->courseLearningMaterials = array_map(function (CourseLearningMaterialInterface $clm) {
                $lm = $clm->getLearningMaterial();
                return $lm->getTitle() . ' ' . $lm->getDescription();
            }, $course->getLearningMaterials()->toArray());
            foreach ($course->getSessions() as $session) {
                $sessionArr = [
                    'sessionId' => $session->getId(),
                    'title' => $session->getTitle(),
                    'description' => ''
                ];
                if ($sessionDescription = $session->getSessionDescription()) {
                    $sessionArr['description'] = $sessionDescription->getDescription();
                }
                $sessionArr['sessionType'] = $session->getSessionType()->getTitle();

                $sessionArr['administrators'] = array_map(function (UserInterface $user) {
                    return $user->getFirstAndLastName() . ' ' . $user->getDisplayName();
                }, $session->getAdministrators()->toArray());
                $sessionArr['terms'] = array_map(function (TermInterface $term) {
                    return $term->getTitle();
                }, $session->getTerms()->toArray());
                $sessionArr['objectives'] = array_map(function (ObjectiveInterface $objective) {
                    return $objective->getTitle();
                }, $session->getObjectives()->toArray());
                $sessionArr['meshDescriptors'] = array_map(function (MeshDescriptorInterface $descriptor) {
                    return $descriptor->getId();
                }, $session->getMeshDescriptors()->toArray());
                $sessionArr['learningMaterials'] = array_map(function (SessionLearningMaterialInterface $slm) {
                    $lm = $slm->getLearningMaterial();
                    return $lm->getTitle() . ' ' . $lm->getDescription();
                }, $session->getLearningMaterials()->toArray());

                $index->sessions[] = $sessionArr;
            }
            $this->index->deleteCourse($course->getId());
            $this->index->indexCourses([$index]);
        }
    }
}
