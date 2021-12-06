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
 * Interface SessionObjectiveInterface
 */
interface SessionObjectiveInterface extends
    IdentifiableEntityInterface,
    IndexableCoursesEntityInterface,
    SessionStampableInterface,
    TitledEntityInterface,
    MeshDescriptorsEntityInterface,
    ActivatableEntityInterface,
    CategorizableEntityInterface,
    SortableEntityInterface
{
    public function setSession(SessionInterface $session): void;

    public function getSession(): SessionInterface;

    public function setCourseObjectives(Collection $courseObjectives);

    public function addCourseObjective(CourseObjectiveInterface $courseObjective);

    public function removeCourseObjective(CourseObjectiveInterface $courseObjective);

    public function getCourseObjectives(): Collection;

    public function setAncestor(SessionObjectiveInterface $ancestor);

    public function getAncestor(): SessionObjectiveInterface;

    public function getAncestorOrSelf(): SessionObjectiveInterface;

    public function setDescendants(Collection $children);

    public function addDescendant(SessionObjectiveInterface $child);

    public function removeDescendant(SessionObjectiveInterface $child);

    public function getDescendants(): Collection;
}
