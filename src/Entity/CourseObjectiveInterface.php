<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\ActivatableEntityInterface;
use App\Traits\IndexableCoursesEntityInterface;
use App\Traits\MeshDescriptorsEntityInterface;
use App\Traits\ObjectiveRelationshipInterface;
use App\Traits\TitledEntityInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface CourseObjectiveInterface
 */
interface CourseObjectiveInterface extends
    ObjectiveRelationshipInterface,
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
     * @param Collection $parents
     */
    public function setParents(Collection $parents);

    /**
     * @param ProgramYearObjectiveInterface $parent
     */
    public function addParent(ProgramYearObjectiveInterface $parent);

    /**
     * @param ProgramYearObjectiveInterface $parent
     */
    public function removeParent(ProgramYearObjectiveInterface $parent);

    /**
     * @return Collection
     */
    public function getParents();

    /**
     * @param Collection $children
     */
    public function setChildren(Collection $children);

    /**
     * @param SessionObjectiveInterface $child
     */
    public function addChild(SessionObjectiveInterface $child);

    /**
     * @param SessionObjectiveInterface $child
     */
    public function removeChild(SessionObjectiveInterface $child);

    /**
     * @return Collection
     */
    public function getChildren();

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
