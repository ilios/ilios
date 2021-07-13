<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\ActivatableEntityInterface;
use App\Traits\MeshDescriptorsEntityInterface;
use App\Traits\TitledEntityInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface ProgramYearObjectiveInterface
 */
interface ProgramYearObjectiveInterface extends
    TitledEntityInterface,
    MeshDescriptorsEntityInterface,
    ActivatableEntityInterface
{
    public function setProgramYear(ProgramYearInterface $programYear): void;

    public function getProgramYear(): ProgramYearInterface;

    public function setCompetency(CompetencyInterface $competency);

    /**
     * @return CompetencyInterface
     */
    public function getCompetency();

    public function setCourseObjectives(Collection $courseObjectives);

    public function addCourseObjective(CourseObjectiveInterface $courseObjective);

    public function removeCourseObjective(CourseObjectiveInterface $courseObjective);

    /**
     * @return Collection
     */
    public function getCourseObjectives();

    public function setAncestor(ProgramYearObjectiveInterface $ancestor);

    /**
     * @return ProgramYearObjectiveInterface
     */
    public function getAncestor();

    /**
     * @return ProgramYearObjectiveInterface
     */
    public function getAncestorOrSelf();

    public function setDescendants(Collection $children);

    public function addDescendant(ProgramYearObjectiveInterface $child);

    public function removeDescendant(ProgramYearObjectiveInterface $child);

    /**
     * @return Collection
     */
    public function getDescendants();
}
