<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\SchoolEntityInterface;

/**
 * Interface ProgramYearStewardInterface
 * @package Ilios\CoreBundle\Entity
 */
interface ProgramYearStewardInterface extends
    IdentifiableEntityInterface,
    SchoolEntityInterface
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
}
