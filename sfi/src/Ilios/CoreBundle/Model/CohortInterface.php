<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface CohortInterface
 */
interface CohortInterface 
{
    public function getCohortId();

    public function setTitle($title);

    public function getTitle();

    public function setProgramYear(\Ilios\CoreBundle\Model\ProgramYear $programYear = null);

    public function getProgramYear();

    public function addCourse(\Ilios\CoreBundle\Model\Course $courses);

    public function removeCourse(\Ilios\CoreBundle\Model\Course $courses);

    public function getCourses();
}
