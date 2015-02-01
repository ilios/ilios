<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

/**
 * Interface ProgramYearStewardInterface
 * @package Ilios\CoreBundle\Entity
 */
interface ProgramYearStewardInterface extends IdentifiableEntityInterface
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
     * @param SchoolInterface $school
     */
    public function setSchool(SchoolInterface $school);

    /**
     * @return SchoolInterface
     */
    public function getSchool();
}