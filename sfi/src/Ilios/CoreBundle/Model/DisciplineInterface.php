<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface DisciplineInterface
 */
interface DisciplineInterface 
{
    public function getDisciplineId();

    public function setTitle($title);

    public function getTitle();

    public function setOwningSchool(\Ilios\CoreBundle\Model\School $school = null);

    public function getOwningSchool();

    public function addCourse(\Ilios\CoreBundle\Model\Course $courses);

    public function removeCourse(\Ilios\CoreBundle\Model\Course $courses);

    public function getCourses();

    public function addProgramYear(\Ilios\CoreBundle\Model\ProgramYear $programYears);

    public function removeProgramYear(\Ilios\CoreBundle\Model\ProgramYear $programYears);

    public function getProgramYears();

    public function addSession(\Ilios\CoreBundle\Model\Session $sessions);

    public function removeSession(\Ilios\CoreBundle\Model\Session $sessions);

    public function getSessions();
}
