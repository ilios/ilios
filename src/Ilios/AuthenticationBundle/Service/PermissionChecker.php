<?php

namespace Ilios\AuthenticationBundle\Service;


use Ilios\CoreBundle\Entity\DTO\SchoolDTO;
use Ilios\CoreBundle\Entity\Manager\SchoolManager;

class PermissionChecker
{
    /**
     * @var SchoolManager
     */
    private $schoolManager;

    /**
     * @var array
     */
    private $matrix;

    public function __construct(SchoolManager $schoolManager)
    {
        $this->schoolManager = $schoolManager;
        $schoolDtos = $this->schoolManager->findDTOsBy([]);
        $this->matrix = [];
        /** @var SchoolDTO $schoolDto */
        foreach ($schoolDtos as $schoolDto) {
            $arr = [];
            $arr['canSchoolDirectorReadAllCourses'] = true;
            $arr['canSchoolAdministratorReadAllCourses'] = true;
            $arr['canCourseDirectorsReadAllCourses'] = true;
            $arr['canCourseAdministratorsReadAllCourses'] = true;
            $arr['canSessionAdministratorsReadAllCourses'] = true;
            $arr['canCourseInstructorsReadAllCourses'] = true;
            $arr['canCourseDirectorsReadTheirCourse'] = true;
            $arr['canCourseAdministratorsReadTheirCourse'] = true;
            $arr['canSessionAdministratorsReadTheirCourse'] = true;
            $arr['canCourseInstructorsReadTheirCourse'] = true;

            $this->matrix[$schoolDto->id] = $arr;
        }
    }

    public function canSchoolDirectorReadAllCourses(int $schoolId) : bool
    {
        return $this->matrix[$schoolId]['canSchoolDirectorReadAllCourses'];
    }

    public function canSchoolAdministratorReadAllCourses(int $schoolId) : bool
    {
        return $this->matrix[$schoolId]['canSchoolAdministratorReadAllCourses'];
    }

    public function canCourseDirectorsReadAllCourses(int $schoolId) : bool
    {
        return $this->matrix[$schoolId]['canCourseDirectorsReadAllCourses'];
    }

    public function canCourseAdministratorsReadAllCourses(int $schoolId) : bool
    {
        return $this->matrix[$schoolId]['canCourseAdministratorsReadAllCourses'];
    }

    public function canSessionAdministratorsReadAllCourses(int $schoolId) : bool
    {
        return $this->matrix[$schoolId]['canSessionAdministratorsReadAllCourses'];
    }

    public function canCourseInstructorsReadAllCourses(int $schoolId) : bool
    {
        return $this->matrix[$schoolId]['canCourseInstructorsReadAllCourses'];
    }

    public function canCourseDirectorsReadTheirCourse(int $schoolId) : bool
    {
        return $this->matrix[$schoolId]['canCourseDirectorsReadTheirCourse'];
    }

    public function canCourseAdministratorsReadTheirCourse(int $schoolId) : bool
    {
        return $this->matrix[$schoolId]['canCourseAdministratorsReadTheirCourse'];
    }

    public function canSessionAdministratorsReadTheirCourse(int $schoolId) : bool
    {
        return $this->matrix[$schoolId]['canSessionAdministratorsReadTheirCourse'];
    }

    public function canCourseInstructorsReadTheirCourse(int $schoolId) : bool
    {
        return $this->matrix[$schoolId]['canCourseInstructorsReadTheirCourse'];
    }

}