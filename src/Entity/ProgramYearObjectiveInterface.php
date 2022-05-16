<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\ActivatableEntityInterface;
use App\Traits\CategorizableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\MeshDescriptorsEntityInterface;
use App\Traits\SortableEntityInterface;
use App\Traits\TitledEntityInterface;
use Doctrine\Common\Collections\Collection;

interface ProgramYearObjectiveInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    MeshDescriptorsEntityInterface,
    ActivatableEntityInterface,
    CategorizableEntityInterface,
    SortableEntityInterface
{
    public function setProgramYear(ProgramYearInterface $programYear): void;
    public function getProgramYear(): ProgramYearInterface;

    public function setCompetency(CompetencyInterface $competency);
    public function getCompetency(): ?CompetencyInterface;

    public function setCourseObjectives(Collection $courseObjectives);
    public function addCourseObjective(CourseObjectiveInterface $courseObjective);
    public function removeCourseObjective(CourseObjectiveInterface $courseObjective);
    public function getCourseObjectives(): Collection;

    public function setAncestor(ProgramYearObjectiveInterface $ancestor);
    public function getAncestor(): ?ProgramYearObjectiveInterface;

    public function getAncestorOrSelf(): ProgramYearObjectiveInterface;

    public function setDescendants(Collection $children);
    public function addDescendant(ProgramYearObjectiveInterface $child);
    public function removeDescendant(ProgramYearObjectiveInterface $child);
    public function getDescendants(): Collection;
}
