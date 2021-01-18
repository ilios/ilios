<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\ActivatableEntityInterface;
use App\Traits\IndexableCoursesEntityInterface;
use App\Traits\MeshDescriptorsEntityInterface;
use App\Traits\TitledEntityInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface SessionObjectiveInterface
 */
interface SessionObjectiveInterface extends
    IndexableCoursesEntityInterface,
    SessionStampableInterface,
    TitledEntityInterface,
    MeshDescriptorsEntityInterface,
    ActivatableEntityInterface
{
    /**
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session): void;

    /**
     * @return SessionInterface
     */
    public function getSession(): SessionInterface;

    /**
     * @param Collection $courseObjectives
     */
    public function setCourseObjectives(Collection $courseObjectives);

    /**
     * @param CourseObjectiveInterface $courseObjective
     */
    public function addCourseObjective(CourseObjectiveInterface $courseObjective);

    /**
     * @param CourseObjectiveInterface $courseObjective
     */
    public function removeCourseObjective(CourseObjectiveInterface $courseObjective);

    /**
     * @return Collection
     */
    public function getCourseObjectives();

    /**
     * @param SessionObjectiveInterface $ancestor
     */
    public function setAncestor(SessionObjectiveInterface $ancestor);

    /**
     * @return SessionObjectiveInterface
     */
    public function getAncestor();

    /**
     * @return SessionObjectiveInterface
     */
    public function getAncestorOrSelf();

    /**
     * @param Collection $children
     */
    public function setDescendants(Collection $children);

    /**
     * @param SessionObjectiveInterface $child
     */
    public function addDescendant(SessionObjectiveInterface $child);

    /**
     * @param SessionObjectiveInterface $child
     */
    public function removeDescendant(SessionObjectiveInterface $child);

    /**
     * @return Collection
     */
    public function getDescendants();
}
