<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\IlmSessionsEntityInterface;
use Ilios\CoreBundle\Traits\InstructorGroupsEntityInterface;
use Ilios\CoreBundle\Traits\InstructorsEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;
use Ilios\CoreBundle\Traits\OfferingsEntityInterface;
use Ilios\CoreBundle\Traits\UsersEntityInterface;

/**
 * Interface GroupInterface
 */
interface LearnerGroupInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    StringableEntityInterface,
    OfferingsEntityInterface,
    LoggableEntityInterface,
    UsersEntityInterface,
    InstructorGroupsEntityInterface,
    InstructorsEntityInterface,
    IlmSessionsEntityInterface
{
    /**
     * @param string $location
     */
    public function setLocation($location);

    /**
     * @return string
     */
    public function getLocation();

    /**
     * @param CohortInterface $cohort
     */
    public function setCohort(CohortInterface $cohort);

    /**
     * @return CohortInterface
     */
    public function getCohort();

    /**
     * @param LearnerGroupInterface $parent
     */
    public function setParent(LearnerGroupInterface $parent = null);

    /**
     * @return LearnerGroupInterface
     */
    public function getParent();

    /**
     * @param Collection $children
     */
    public function setChildren(Collection $children);

    /**
     * @param LearnerGroupInterface $child
     */
    public function addChild(LearnerGroupInterface $child);

    /**
     * @param LearnerGroupInterface $child
     */
    public function removeChild(LearnerGroupInterface $child);

    /**
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function getChildren();

    /**
     * Get the school we belong to
     * @return SchoolInterface|null
     */
    public function getSchool();

    /**
     * Gets the program that this learner group belongs to.
     * @return ProgramInterface|null
     */
    public function getProgram();

    /**
     * Gets the program year that this learner group belongs to.
     * @return ProgramYearInterface|null
     */
    public function getProgramYear();
}
