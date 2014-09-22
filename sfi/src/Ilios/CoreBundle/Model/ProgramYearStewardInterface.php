<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface ProgramYearStewardInterface
 */
interface ProgramYearStewardInterface 
{
    public function getProgramYearStewardId();

    public function setDepartment(\Ilios\CoreBundle\Model\Department $department = null);

    public function getDepartment();

    public function setProgramYear(\Ilios\CoreBundle\Model\ProgramYear $programYear = null);

    public function getProgramYear();

    public function setSchool(\Ilios\CoreBundle\Model\School $school = null);

    public function getSchool();
}
