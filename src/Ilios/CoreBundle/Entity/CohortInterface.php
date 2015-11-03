<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

use Ilios\CoreBundle\Traits\TitledEntityInterface;
use Ilios\CoreBundle\Traits\CoursesEntityInterface;

/**
 * Interface CohortInterface
 */
interface CohortInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    CoursesEntityInterface,
    LoggableEntityInterface
{
    /**
     * @param ProgramYearInterface $programYear
     */
    public function setProgramYear(ProgramYearInterface $programYear = null);

    /**
     * @return ProgramYearInterface
     */
    public function getProgramYear();

    /**
     * @return LearnerGroupInterface[]|ArrayCollection
     */
    public function getLearnerGroups();

    /**
    * @param Collection $learnerGroups
    */
    public function setLearnerGroups(Collection $learnerGroups);

    /**
    * @param LearnerGroupInterface $learnerGroup
    */
    public function addLearnerGroup(LearnerGroupInterface $learnerGroup);

    /**
     * @param Collection $users
     */
    public function setUsers(Collection $users = null);

    /**
     * @param UserInterface $user
     */
    public function addUser(UserInterface $user);

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getUsers();


    /**
     * Get the school we belong to
     * @return SchoolInterface|null
     */
    public function getSchool();

    /**
     * Gets the program that this cohort belongs to.
     * @return ProgramInterface|null
     */
    public function getProgram();
}
