<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

use Ilios\CoreBundle\Traits\LearnerGroupsEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;
use Ilios\CoreBundle\Traits\CoursesEntityInterface;
use Ilios\CoreBundle\Traits\UsersEntityInterface;

/**
 * Interface CohortInterface
 */
interface CohortInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    CoursesEntityInterface,
    LoggableEntityInterface,
    LearnerGroupsEntityInterface,
    UsersEntityInterface
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
