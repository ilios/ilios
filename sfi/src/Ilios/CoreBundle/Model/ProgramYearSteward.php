<?php

namespace Ilios\CoreBundle\Model;



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
     * @var \Ilios\CoreBundle\Model\Department
     */
    private $department;

    /**
     * @var \Ilios\CoreBundle\Model\ProgramYear
     */
    private $programYear;

    /**
     * @var \Ilios\CoreBundle\Model\School
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
     * @param \Ilios\CoreBundle\Model\Department $department
     * @return ProgramYearSteward
     */
    public function setDepartment(\Ilios\CoreBundle\Model\Department $department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return \Ilios\CoreBundle\Model\Department 
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set programYear
     *
     * @param \Ilios\CoreBundle\Model\ProgramYear $programYear
     * @return ProgramYearSteward
     */
    public function setProgramYear(\Ilios\CoreBundle\Model\ProgramYear $programYear = null)
    {
        $this->programYear = $programYear;

        return $this;
    }

    /**
     * Get programYear
     *
     * @return \Ilios\CoreBundle\Model\ProgramYear 
     */
    public function getProgramYear()
    {
        return $this->programYear;
    }

    /**
     * Set school
     *
     * @param \Ilios\CoreBundle\Model\School $school
     * @return ProgramYearSteward
     */
    public function setSchool(\Ilios\CoreBundle\Model\School $school = null)
    {
        $this->school = $school;

        return $this;
    }

    /**
     * Get school
     *
     * @return \Ilios\CoreBundle\Model\School 
     */
    public function getSchool()
    {
        return $this->school;
    }
}
