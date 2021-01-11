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
    /**
     * @param ProgramYearInterface $programYear
     */
    public function setProgramYear(ProgramYearInterface $programYear): void;

    /**
     * @return ProgramYearInterface
     */
    public function getProgramYear(): ProgramYearInterface;

    /**
     * @param CompetencyInterface $competency
     */
    public function setCompetency(CompetencyInterface $competency);

    /**
     * @return CompetencyInterface
     */
    public function getCompetency();

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
     * @param ProgramYearObjectiveInterface $ancestor
     */
    public function setAncestor(ProgramYearObjectiveInterface $ancestor);

    /**
     * @return ProgramYearObjectiveInterface
     */
    public function getAncestor();

    /**
     * @return ProgramYearObjectiveInterface
     */
    public function getAncestorOrSelf();

    /**
     * @param Collection $children
     */
    public function setDescendants(Collection $children);

    /**
     * @param ProgramYearObjectiveInterface $child
     */
    public function addDescendant(ProgramYearObjectiveInterface $child);

    /**
     * @param ProgramYearObjectiveInterface $child
     */
    public function removeDescendant(ProgramYearObjectiveInterface $child);

    /**
     * @return Collection
     */
    public function getDescendants();
}
