<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\SchoolEntityInterface;

/**
 * Interface ProgramYearStewardInterface
 */
interface ProgramYearStewardInterface extends
    IdentifiableEntityInterface,
    SchoolEntityInterface,
    LoggableEntityInterface
{
    /**
     * @param DepartmentInterface $department
     */
    public function setDepartment(DepartmentInterface $department);

    /**
     * @return DepartmentInterface
     */
    public function getDepartment();

    /**
     * @param ProgramYearInterface $programYear
     */
    public function setProgramYear(ProgramYearInterface $programYear);

    /**
     * @return ProgramYearInterface
     */
    public function getProgramYear();

    /**
     * Gets the program owning the stewarded program year.
     * @return ProgramInterface|null
     */
    public function getProgram();

    /**
     * Gets the school that program owning the stewarded program year belongs to.
     * @return SchoolInterface|null
     */
    public function getProgramOwningSchool();
}
