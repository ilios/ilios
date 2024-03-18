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
use Stringable;

interface CourseObjectiveInterface extends
    IdentifiableEntityInterface,
    IndexableCoursesEntityInterface,
    TitledEntityInterface,
    MeshDescriptorsEntityInterface,
    ActivatableEntityInterface,
    CategorizableEntityInterface,
    SortableEntityInterface,
    Stringable
{
    public function setCourse(CourseInterface $course): void;
    public function getCourse(): CourseInterface;

    public function setProgramYearObjectives(Collection $programYearObjectives): void;
    public function addProgramYearObjective(ProgramYearObjectiveInterface $programYearObjective): void;
    public function removeProgramYearObjective(ProgramYearObjectiveInterface $programYearObjective): void;
    public function getProgramYearObjectives(): Collection;

    public function setSessionObjectives(Collection $sessionObjectives): void;
    public function addSessionObjective(SessionObjectiveInterface $sessionObjective): void;
    public function removeSessionObjective(SessionObjectiveInterface $sessionObjective): void;
    public function getSessionObjectives(): Collection;

    public function setAncestor(?CourseObjectiveInterface $ancestor = null): void;
    public function getAncestor(): ?CourseObjectiveInterface;
    public function getAncestorOrSelf(): CourseObjectiveInterface;

    public function setDescendants(Collection $descendants): void;
    public function addDescendant(CourseObjectiveInterface $descendant): void;
    public function removeDescendant(CourseObjectiveInterface $descendant): void;
    public function getDescendants(): Collection;
}
