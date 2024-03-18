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
use Stringable;

interface ProgramYearObjectiveInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    MeshDescriptorsEntityInterface,
    ActivatableEntityInterface,
    CategorizableEntityInterface,
    SortableEntityInterface,
    Stringable
{
    public function setProgramYear(ProgramYearInterface $programYear): void;
    public function getProgramYear(): ProgramYearInterface;

    public function setCompetency(CompetencyInterface $competency): void;
    public function getCompetency(): ?CompetencyInterface;

    public function setCourseObjectives(Collection $courseObjectives): void;
    public function addCourseObjective(CourseObjectiveInterface $courseObjective): void;
    public function removeCourseObjective(CourseObjectiveInterface $courseObjective): void;
    public function getCourseObjectives(): Collection;

    public function setAncestor(ProgramYearObjectiveInterface $ancestor): void;
    public function getAncestor(): ?ProgramYearObjectiveInterface;

    public function getAncestorOrSelf(): ProgramYearObjectiveInterface;

    public function setDescendants(Collection $descendants): void;
    public function addDescendant(ProgramYearObjectiveInterface $descendant): void;
    public function removeDescendant(ProgramYearObjectiveInterface $descendant): void;
    public function getDescendants(): Collection;
}
