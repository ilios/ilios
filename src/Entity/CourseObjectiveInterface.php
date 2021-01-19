<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\ActivatableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\IndexableCoursesEntityInterface;
use App\Traits\MeshDescriptorsEntityInterface;
use App\Traits\TitledEntityInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface CourseObjectiveInterface
 */
interface CourseObjectiveInterface extends
    IdentifiableEntityInterface,
    IndexableCoursesEntityInterface,
    TitledEntityInterface,
    MeshDescriptorsEntityInterface,
    ActivatableEntityInterface
{
    /**
     * @param CourseInterface $course
     */
    public function setCourse(CourseInterface $course): void;

    /**
     * @return CourseInterface
     */
    public function getCourse(): CourseInterface;

    /**
     * @param Collection $programYearObjectives
     */
    public function setProgramYearObjectives(Collection $programYearObjectives);

    /**
     * @param ProgramYearObjectiveInterface $programYearObjective
     */
    public function addProgramYearObjective(ProgramYearObjectiveInterface $programYearObjective);

    /**
     * @param ProgramYearObjectiveInterface $programYearObjective
     */
    public function removeProgramYearObjective(ProgramYearObjectiveInterface $programYearObjective);

    /**
     * @return Collection
     */
    public function getProgramYearObjectives();

    /**
     * @param Collection $sessionObjectives
     */
    public function setSessionObjectives(Collection $sessionObjectives);

    /**
     * @param SessionObjectiveInterface $sessionObjective
     */
    public function addSessionObjective(SessionObjectiveInterface $sessionObjective);

    /**
     * @param SessionObjectiveInterface $sessionObjective
     */
    public function removeSessionObjective(SessionObjectiveInterface $sessionObjective);

    /**
     * @return Collection
     */
    public function getSessionObjectives();

    /**
     * @param CourseObjectiveInterface $ancestor
     */
    public function setAncestor(CourseObjectiveInterface $ancestor);

    /**
     * @return CourseObjectiveInterface
     */
    public function getAncestor();

    /**
     * @return CourseObjectiveInterface
     */
    public function getAncestorOrSelf();

    /**
     * @param Collection $children
     */
    public function setDescendants(Collection $children);

    /**
     * @param CourseObjectiveInterface $child
     */
    public function addDescendant(CourseObjectiveInterface $child);

    /**
     * @param CourseObjectiveInterface $child
     */
    public function removeDescendant(CourseObjectiveInterface $child);

    /**
     * @return Collection
     */
    public function getDescendants();
}
