<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\ActivatableEntityInterface;
use App\Entity\CategorizableEntityInterface;
use App\Entity\IdentifiableEntityInterface;
use App\Entity\IndexableCoursesEntityInterface;
use App\Entity\MeshDescriptorsEntityInterface;
use App\Entity\SortableEntityInterface;
use App\Entity\TitledEntityInterface;
use Doctrine\Common\Collections\Collection;
use Stringable;

interface SessionObjectiveInterface extends
    IdentifiableEntityInterface,
    IndexableCoursesEntityInterface,
    SessionStampableInterface,
    TitledEntityInterface,
    MeshDescriptorsEntityInterface,
    ActivatableEntityInterface,
    CategorizableEntityInterface,
    SortableEntityInterface,
    Stringable
{
    public function setSession(SessionInterface $session): void;
    public function getSession(): SessionInterface;

    public function setCourseObjectives(Collection $courseObjectives): void;
    public function addCourseObjective(CourseObjectiveInterface $courseObjective): void;
    public function removeCourseObjective(CourseObjectiveInterface $courseObjective): void;
    public function getCourseObjectives(): Collection;

    public function setAncestor(?SessionObjectiveInterface $ancestor = null): void;
    public function getAncestor(): ?SessionObjectiveInterface;

    public function getAncestorOrSelf(): SessionObjectiveInterface;

    public function setDescendants(Collection $descendants): void;
    public function addDescendant(SessionObjectiveInterface $descendant): void;
    public function removeDescendant(SessionObjectiveInterface $descendant): void;
    public function getDescendants(): Collection;
}
