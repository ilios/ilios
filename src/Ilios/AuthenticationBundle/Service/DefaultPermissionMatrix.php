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

        /** @var SchoolDTO $schoolDto */
        foreach ($schoolDtos as $schoolDto) {
            $schoolId = $schoolDto->id;

            $this->setPermission($schoolId, Capabilities::CAN_UPDATE_ALL_COURSES, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_CREATE_COURSES, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_DELETE_ALL_COURSES, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_UNLOCK_ALL_COURSES, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_UNARCHIVE_ALL_COURSES, $allRoles);

            $this->setPermission($schoolId, Capabilities::CAN_UPDATE_THEIR_COURSES, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_DELETE_THEIR_COURSES, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_UNLOCK_THEIR_COURSES, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_UNARCHIVE_THEIR_COURSES, $allRoles);

            $this->setPermission($schoolId, Capabilities::CAN_UPDATE_ALL_SESSIONS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_CREATE_SESSIONS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_DELETE_ALL_SESSIONS, $allRoles);

            $this->setPermission($schoolId, Capabilities::CAN_UPDATE_THEIR_SESSIONS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_DELETE_THEIR_SESSIONS, $allRoles);

            $this->setPermission($schoolId, Capabilities::CAN_UPDATE_SESSION_TYPES, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_CREATE_SESSION_TYPES, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_DELETE_SESSION_TYPES, $allRoles);

            $this->setPermission($schoolId, Capabilities::CAN_UPDATE_DEPARTMENTS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_CREATE_DEPARTMENTS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_DELETE_DEPARTMENTS, $allRoles);

            $this->setPermission($schoolId, Capabilities::CAN_UPDATE_SCHOOL_CONFIGS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_CREATE_SCHOOL_CONFIGS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_DELETE_SCHOOL_CONFIGS, $allRoles);

            $this->setPermission($schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_CREATE_PROGRAMS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_DELETE_ALL_PROGRAMS, $allRoles);

            $this->setPermission($schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAMS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_DELETE_THEIR_PROGRAMS, $allRoles);

            $this->setPermission($schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAM_YEARS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_CREATE_PROGRAM_YEARS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_DELETE_ALL_PROGRAM_YEARS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_UNLOCK_ALL_PROGRAM_YEARS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_UNARCHIVE_ALL_PROGRAM_YEARS, $allRoles);

            $this->setPermission($schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAM_YEARS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_DELETE_THEIR_PROGRAM_YEARS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_UNLOCK_THEIR_PROGRAM_YEARS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_UNARCHIVE_THEIR_PROGRAM_YEARS, $allRoles);

            $this->setPermission($schoolId, Capabilities::CAN_UPDATE_ALL_COHORTS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_CREATE_COHORTS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_DELETE_ALL_COHORTS, $allRoles);

            $this->setPermission($schoolId, Capabilities::CAN_UPDATE_THEIR_COHORTS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_DELETE_THEIR_COHORTS, $allRoles);
            ;
            $this->setPermission($schoolId, Capabilities::CAN_UPDATE_SCHOOL_CONFIGS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_CREATE_SCHOOL_CONFIGS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_DELETE_SCHOOL_CONFIGS, $allRoles);

            $this->setPermission($schoolId, Capabilities::CAN_UPDATE_SCHOOLS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_DELETE_SCHOOLS, $allRoles);

            $this->setPermission($schoolId, Capabilities::CAN_UPDATE_COMPETENCIES, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_CREATE_COMPETENCIES, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_DELETE_COMPETENCIES, $allRoles);

            $this->setPermission($schoolId, Capabilities::CAN_UPDATE_VOCABULARIES, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_CREATE_VOCABULARIES, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_DELETE_VOCABULARIES, $allRoles);

            $this->setPermission($schoolId, Capabilities::CAN_UPDATE_TERMS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_CREATE_TERMS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_DELETE_TERMS, $allRoles);

            $this->setPermission($schoolId, Capabilities::CAN_UPDATE_INSTRUCTOR_GROUPS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_CREATE_INSTRUCTOR_GROUPS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_DELETE_INSTRUCTOR_GROUPS, $allRoles);

            $this->setPermission(
                $schoolId,
                Capabilities::CAN_UPDATE_ALL_CURRICULUM_INVENTORY_REPORTS,
                $allRoles
            );
            $this->setPermission(
                $schoolId,
                Capabilities::CAN_CREATE_CURRICULUM_INVENTORY_REPORTS,
                $allRoles
            );
            $this->setPermission(
                $schoolId,
                Capabilities::CAN_DELETE_ALL_CURRICULUM_INVENTORY_REPORTS,
                $allRoles
            );

            $this->setPermission(
                $schoolId,
                Capabilities::CAN_UPDATE_THEIR_CURRICULUM_INVENTORY_REPORTS,
                $allRoles
            );
            $this->setPermission(
                $schoolId,
                Capabilities::CAN_DELETE_THEIR_CURRICULUM_INVENTORY_REPORTS,
                $allRoles
            );

            $this->setPermission(
                $schoolId,
                Capabilities::CAN_UPDATE_CURRICULUM_INVENTORY_INSTITUTIONS,
                $allRoles
            );
            $this->setPermission(
                $schoolId,
                Capabilities::CAN_CREATE_CURRICULUM_INVENTORY_INSTITUTIONS,
                $allRoles
            );
            $this->setPermission(
                $schoolId,
                Capabilities::CAN_DELETE_CURRICULUM_INVENTORY_INSTITUTIONS,
                $allRoles
            );

            $this->setPermission($schoolId, Capabilities::CAN_UPDATE_LEARNER_GROUPS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_CREATE_LEARNER_GROUPS, $allRoles);
            $this->setPermission($schoolId, Capabilities::CAN_DELETE_LEARNER_GROUPS, $allRoles);
        }
    }
}
