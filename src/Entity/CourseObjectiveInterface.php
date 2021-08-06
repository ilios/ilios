<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\ActivatableEntityInterface;
use App\Traits\CategorizableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\IndexableCoursesEntityInterface;
use App\Traits\MeshDescriptorsEntityInterface;
use App\Traits\SortableEntityInterface;
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
    ActivatableEntityInterface,
    CategorizableEntityInterface,
    SortableEntityInterface
{
    public function setCourse(CourseInterface $course): void;

    public function getCourse(): CourseInterface;

    public function setProgramYearObjectives(Collection $programYearObjectives);

    public function addProgramYearObjective(ProgramYearObjectiveInterface $programYearObjective);

    public function removeProgramYearObjective(ProgramYearObjectiveInterface $programYearObjective);

    /**
     * @return Collection
     */
    public function getProgramYearObjectives();

    public function setSessionObjectives(Collection $sessionObjectives);

    public function addSessionObjective(SessionObjectiveInterface $sessionObjective);

    public function removeSessionObjective(SessionObjectiveInterface $sessionObjective);

    /**
     * @return Collection
     */
    public function getSessionObjectives();

    public function setAncestor(CourseObjectiveInterface $ancestor);

    /**
     * @return CourseObjectiveInterface
     */
    public function getAncestor();

    /**
     * @return CourseObjectiveInterface
     */
    public function getAncestorOrSelf();

    public function setDescendants(Collection $children);

    public function addDescendant(CourseObjectiveInterface $child);

    public function removeDescendant(CourseObjectiveInterface $child);

    /**
     * @return Collection
     */
    public function getDescendants();
}
