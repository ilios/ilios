<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;
use Ilios\CoreBundle\Traits\CoursesEntityInterface;
use Ilios\CoreBundle\Traits\SessionsEntityInterface;
use Ilios\CoreBundle\Traits\ProgramYearsEntityInterface;

/**
 * Interface ObjectiveInterface
 */
interface ObjectiveInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    CoursesEntityInterface,
    SessionsEntityInterface,
    ProgramYearsEntityInterface,
    LoggableEntityInterface
{
    /**
     * @param CompetencyInterface $competency
     */
    public function setCompetency(CompetencyInterface $competency);

    /**
     * @return CompetencyInterface
     */
    public function getCompetency();

    /**
     * @param Collection $parents
     */
    public function setParents(Collection $parents);

    /**
     * @param ObjectiveInterface $parent
     */
    public function addParent(ObjectiveInterface $parent);

    /**
     * @return ArrayCollection|ObjectiveInterface
     */
    public function getParents();

    /**
     * @param Collection $children
     */
    public function setChildren(Collection $children);

    /**
     * @param ObjectiveInterface $child
     */
    public function addChild(ObjectiveInterface $child);

    /**
     * @return ArrayCollection|ObjectiveInterface[]
     */
    public function getChildren();

    /**
     * @param Collection $meshDescriptors
     */
    public function setMeshDescriptors(Collection $meshDescriptors);

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     */
    public function addMeshDescriptor(MeshDescriptorInterface $meshDescriptor);

    /**
     * @return ArrayCollection|MeshDescriptorInterface[]
     */
    public function getMeshDescriptors();

    /**
     * @param ObjectiveInterface $ancestor
     */
    public function setAncestor(ObjectiveInterface $ancestor);

    /**
     * @return ObjectiveInterface
     */
    public function getAncestor();

    /**
     * @param Collection $children
     */
    public function setDescendants(Collection $children);

    /**
     * @param ObjectiveInterface $child
     */
    public function addDescendant(ObjectiveInterface $child);

    /**
     * @return ArrayCollection|ObjectiveInterface[]
     */
    public function getDescendants();
}
