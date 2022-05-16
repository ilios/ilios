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
    public function getProgramYearObjectives(): Collection;

    public function setSessionObjectives(Collection $sessionObjectives);
    public function addSessionObjective(SessionObjectiveInterface $sessionObjective);
    public function removeSessionObjective(SessionObjectiveInterface $sessionObjective);
    public function getSessionObjectives(): Collection;

    public function setAncestor(CourseObjectiveInterface $ancestor);
    public function getAncestor(): ?CourseObjectiveInterface;
    public function getAncestorOrSelf(): CourseObjectiveInterface;

    public function setDescendants(Collection $children);
    public function addDescendant(CourseObjectiveInterface $child);
    public function removeDescendant(CourseObjectiveInterface $child);
    public function getDescendants(): Collection;
}
