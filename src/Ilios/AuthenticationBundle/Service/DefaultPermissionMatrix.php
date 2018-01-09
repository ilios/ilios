<?php

namespace Ilios\AuthenticationBundle\Service;

use Ilios\AuthenticationBundle\Classes\Capabilities;
use Ilios\AuthenticationBundle\Classes\PermissionMatrix;
use Ilios\AuthenticationBundle\Classes\UserRoles;
use Ilios\CoreBundle\Entity\DTO\SchoolDTO;
use Ilios\CoreBundle\Entity\Manager\SchoolManager;

class DefaultPermissionMatrix extends PermissionMatrix
{
    /**
     * @var SchoolManager
     */
    protected $schoolManager;

    /**
     * @param SchoolManager $schoolManager
     */
    public function __construct(SchoolManager $schoolManager)
    {
        $this->schoolManager = $schoolManager;
        $schoolDtos = $this->schoolManager->findDTOsBy([]);

        /** @var SchoolDTO $schoolDto */
        foreach ($schoolDtos as $schoolDto) {
            $arr = [];
            $allRoles = [
                UserRoles::COURSE_ADMINISTRATOR,
                UserRoles::COURSE_DIRECTOR,
                UserRoles::COURSE_INSTRUCTOR,
                UserRoles::SCHOOL_ADMINISTRATOR,
                UserRoles::SCHOOL_DIRECTOR,
                UserRoles::PROGRAM_DIRECTOR,
                UserRoles::PROGRAM_YEAR_DIRECTOR,
                UserRoles::SESSION_ADMINISTRATOR,
                UserRoles::SESSION_INSTRUCTOR,
            ];
            $arr[Capabilities::CAN_UPDATE_ALL_COURSES] = $allRoles;
            $arr[Capabilities::CAN_CREATE_COURSES] = $allRoles;
            $arr[Capabilities::CAN_DELETE_ALL_COURSES] = $allRoles;
            $arr[Capabilities::CAN_UNLOCK_ALL_COURSES] = $allRoles;
            $arr[Capabilities::CAN_UNARCHIVE_ALL_COURSES] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_THEIR_COURSES] = $allRoles;
            $arr[Capabilities::CAN_DELETE_THEIR_COURSES] = $allRoles;
            $arr[Capabilities::CAN_UNLOCK_THEIR_COURSES] = $allRoles;
            $arr[Capabilities::CAN_UNARCHIVE_THEIR_COURSES] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_ALL_SESSIONS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_SESSIONS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_ALL_SESSIONS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_THEIR_SESSIONS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_THEIR_SESSIONS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_SESSION_TYPES] = $allRoles;
            $arr[Capabilities::CAN_CREATE_SESSION_TYPES] = $allRoles;
            $arr[Capabilities::CAN_DELETE_SESSION_TYPES] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_DEPARTMENTS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_DEPARTMENTS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_DEPARTMENTS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_SCHOOL_CONFIGS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_SCHOOL_CONFIGS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_SCHOOL_CONFIGS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_ALL_PROGRAMS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_PROGRAMS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_ALL_PROGRAMS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_THEIR_PROGRAMS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_THEIR_PROGRAMS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_ALL_PROGRAM_YEARS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_PROGRAM_YEARS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_ALL_PROGRAM_YEARS] = $allRoles;
            $arr[Capabilities::CAN_UNLOCK_ALL_PROGRAM_YEARS] = $allRoles;
            $arr[Capabilities::CAN_UNARCHIVE_ALL_PROGRAM_YEARS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_THEIR_PROGRAM_YEARS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_THEIR_PROGRAM_YEARS] = $allRoles;
            $arr[Capabilities::CAN_UNLOCK_THEIR_PROGRAM_YEARS] = $allRoles;
            $arr[Capabilities::CAN_UNARCHIVE_THEIR_PROGRAM_YEARS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_ALL_COHORTS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_COHORTS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_ALL_COHORTS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_THEIR_COHORTS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_THEIR_COHORTS] = $allRoles;
            ;
            $arr[Capabilities::CAN_UPDATE_SCHOOL_CONFIGS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_SCHOOL_CONFIGS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_SCHOOL_CONFIGS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_SCHOOLS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_SCHOOLS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_COMPETENCIES] = $allRoles;
            $arr[Capabilities::CAN_CREATE_COMPETENCIES] = $allRoles;
            $arr[Capabilities::CAN_DELETE_COMPETENCIES] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_VOCABULARIES] = $allRoles;
            $arr[Capabilities::CAN_CREATE_VOCABULARIES] = $allRoles;
            $arr[Capabilities::CAN_DELETE_VOCABULARIES] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_TERMS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_TERMS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_TERMS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_INSTRUCTOR_GROUPS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_INSTRUCTOR_GROUPS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_INSTRUCTOR_GROUPS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_ALL_CURRICULUM_INVENTORY_REPORTS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_CURRICULUM_INVENTORY_REPORTS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_ALL_CURRICULUM_INVENTORY_REPORTS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_THEIR_CURRICULUM_INVENTORY_REPORTS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_THEIR_CURRICULUM_INVENTORY_REPORTS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_CURRICULUM_INVENTORY_INSTITUTIONS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_CURRICULUM_INVENTORY_INSTITUTIONS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_CURRICULUM_INVENTORY_INSTITUTIONS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_LEARNER_GROUPS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_LEARNER_GROUPS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_LEARNER_GROUPS] = $allRoles;

            $this->matrix[$schoolDto->id] = $arr;
        }
    }

}
