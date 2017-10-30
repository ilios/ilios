<?php

namespace Ilios\AuthenticationBundle\Service;


use Ilios\AuthenticationBundle\Classes\UserRoles;
use Ilios\CoreBundle\Entity\DTO\SchoolDTO;
use Ilios\CoreBundle\Entity\Manager\SchoolManager;

class PermissionChecker
{
    /** @var string */
    const CAN_READ_ALL_COURSES = 'canReadAllCourses';
    /** @var string */
    const CAN_UPDATE_ALL_COURSES = 'canUpdateAllCourses';
    /** @var string */
    const CAN_DELETE_ALL_COURSES = 'canDeleteAllCourses';
    /** @var string */
    const CAN_CREATE_COURSES = 'canCreateCourses';
    /** @var string */
    const CAN_READ_THEIR_COURSES = 'canReadTheirCourses';
    /** @var string */
    const CAN_UPDATE_THEIR_COURSES = 'canUpdateTheirCourses';
    /** @var string */
    const CAN_DELETE_THEIR_COURSES = 'canDeleteTheirCourses';
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
            $arr[self::CAN_READ_ALL_COURSES] = [
                UserRoles::SCHOOL_DIRECTOR,
                UserRoles::SCHOOL_ADMINISTRATOR,
                UserRoles::COURSE_DIRECTOR,
                UserRoles::COURSE_ADMINISTRATOR,
                UserRoles::SESSION_ADMINISTRATOR,
            ];
            $arr[self::CAN_UPDATE_ALL_COURSES] = [
                UserRoles::SCHOOL_DIRECTOR,
                UserRoles::SCHOOL_ADMINISTRATOR,
                UserRoles::COURSE_DIRECTOR,
                UserRoles::COURSE_ADMINISTRATOR,
                UserRoles::SESSION_ADMINISTRATOR,
            ];
            $arr[self::CAN_CREATE_COURSES] = [
                UserRoles::SCHOOL_DIRECTOR,
                UserRoles::SCHOOL_ADMINISTRATOR,
                UserRoles::COURSE_DIRECTOR,
                UserRoles::COURSE_ADMINISTRATOR,
                UserRoles::SESSION_ADMINISTRATOR,
            ];
            $arr[self::CAN_DELETE_ALL_COURSES] = [
                UserRoles::SCHOOL_DIRECTOR,
                UserRoles::SCHOOL_ADMINISTRATOR,
                UserRoles::COURSE_DIRECTOR,
                UserRoles::COURSE_ADMINISTRATOR,
                UserRoles::SESSION_ADMINISTRATOR,
            ];

            $arr[self::CAN_READ_THEIR_COURSES] = [
                UserRoles::SCHOOL_DIRECTOR,
                UserRoles::SCHOOL_ADMINISTRATOR,
                UserRoles::COURSE_DIRECTOR,
                UserRoles::COURSE_ADMINISTRATOR,
            ];
            $arr[self::CAN_UPDATE_THEIR_COURSES] = [
                UserRoles::SCHOOL_DIRECTOR,
                UserRoles::SCHOOL_ADMINISTRATOR,
                UserRoles::COURSE_DIRECTOR,
                UserRoles::COURSE_ADMINISTRATOR,
            ];
            $arr[self::CAN_DELETE_THEIR_COURSES] = [
                UserRoles::SCHOOL_DIRECTOR,
                UserRoles::SCHOOL_ADMINISTRATOR,
                UserRoles::COURSE_DIRECTOR,
                UserRoles::COURSE_ADMINISTRATOR,
            ];

            $this->matrix[$schoolDto->id] = $arr;
        }
    }

    /**
     * @param int $schoolId
     * @param string $capability
     * @param array $roles
     * @return bool
     */
    public function hasPermission(int $schoolId, string $capability, array $roles) : bool
    {
        if (!array_key_exists($schoolId, $this->matrix)) {
            return false;
        }
        $schoolPermissions = $this->matrix[$schoolId];
        if (!array_key_exists($capability, $schoolPermissions)) {
            return false;
        };

        $permittedRoles = $schoolPermissions[$capability];

        $hasPermission = false;
        while (!$hasPermission && !empty($roles)) {
            $role = array_pop($roles);
            $hasPermission = in_array($role, $permittedRoles);
        }

        return $hasPermission;
    }
}
