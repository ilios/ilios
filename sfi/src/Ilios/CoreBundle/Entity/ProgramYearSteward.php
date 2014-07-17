<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProgramYearSteward
 */
class ProgramYearSteward
{
    /**
     * @var integer
     */
    private $programYearStewardId;

    /**
     * @var \Ilios\CoreBundle\Entity\Department
     */
    private $department;

    /**
     * @var \Ilios\CoreBundle\Entity\ProgramYear
     */
    private $programYear;

    /**
     * @var \Ilios\CoreBundle\Entity\School
     */
    private $school;

    /**
     * Get programYearStewardId
     *
     * @return integer 
     */
    public function getProgramYearStewardId()
    {
        return $this->programYearStewardId;
    }

    /**
     * Set department
     *
     * @param \Ilios\CoreBundle\Entity\Department $department
     * @return ProgramYearSteward
     */
    public function setDepartment(\Ilios\CoreBundle\Entity\Department $department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return \Ilios\CoreBundle\Entity\Department 
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set programYear
     *
     * @param \Ilios\CoreBundle\Entity\ProgramYear $programYear
     * @return ProgramYearSteward
     */
    public function setProgramYear(\Ilios\CoreBundle\Entity\ProgramYear $programYear = null)
    {
        $this->programYear = $programYear;

        return $this;
    }

    /**
     * Get programYear
     *
     * @return \Ilios\CoreBundle\Entity\ProgramYear 
     */
    public function getProgramYear()
    {
        return $this->programYear;
    }

    /**
     * Set school
     *
     * @param \Ilios\CoreBundle\Entity\School $school
     * @return ProgramYearSteward
     */
    public function setSchool(\Ilios\CoreBundle\Entity\School $school = null)
    {
        $this->school = $school;

        return $this;
    }

    /**
     * Get school
     *
     * @return \Ilios\CoreBundle\Entity\School 
     */
    public function getSchool()
    {
        return $this->school;
    }
}
