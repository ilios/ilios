<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class UserDTO
 * Data transfer object for a user
 * @package Ilios\CoreBundle\Entity\DTO

 */
class UserDTO
{
    /**
     * @var int
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @IS\Type("string")
     */
    public $lastName;

    /**
     * @var string
     * @IS\Type("string")
     */
    public $firstName;

    /**
     * @var string
     * @IS\Type("string")
     */
    public $middleName;

    /**
     * @var string
     * @IS\Type("string")
     */
    public $phone;

    /**
     * @var string
     * @IS\Type("string")
     */
    public $email;

    /**
     * @var boolean
     * @IS\Type("boolean")
     */
    public $enabled;

    /**
     * @var string
     * @IS\Type("string")
     */
    public $campusId;

    /**
     * @var string
     * @IS\Type("string")
     */
    public $otherId;

    /**
     * @var boolean
     * @IS\Type("boolean")
     */
    public $userSyncIgnore;

    /**
     * @var string
     * @IS\Type("string")
     */
    public $icsFeedKey;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $reminders;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $reports;

    /**
     * @var int
     * @IS\Type("string")
     */
    public $school;

    /**
     * @var int
     * @IS\Type("string")
     */
    public $authentication;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $directedCourses;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $administeredCourses;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $learnerGroups;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $instructedLearnerGroups;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $instructorGroups;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $instructorIlmSessions;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $learnerIlmSessions;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $offerings;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $instructedOfferings;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $programYears;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $roles;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $cohorts;

    /**
     * @var int
     * @IS\Type("string")
     */
    public $primaryCohort;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $pendingUserUpdates;

    /**
     * @var boolean
     * @IS\Type("boolean")

     */
    public $root;

    /**
     * @var array
     * @IS\Type("entityCollection")
     **/
    public $permissions;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $directedSchools;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $administeredSchools;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $administeredSessions;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $directedPrograms;

    public function __construct(
        $id,
        $firstName,
        $lastName,
        $middleName,
        $phone,
        $email,
        $enabled,
        $campusId,
        $otherId,
        $userSyncIgnore,
        $icsFeedKey,
        $root
    ) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->middleName = $middleName;
        $this->phone = $phone;
        $this->email = $email;
        $this->enabled = $enabled;
        $this->campusId = $campusId;
        $this->otherId = $otherId;
        $this->userSyncIgnore = $userSyncIgnore;
        $this->icsFeedKey = $icsFeedKey;
        $this->root = $root;

        $this->reminders                = [];
        $this->directedCourses          = [];
        $this->administeredCourses          = [];
        $this->learnerGroups            = [];
        $this->instructedLearnerGroups  = [];
        $this->instructorGroups         = [];
        $this->offerings                = [];
        $this->instructedOfferings      = [];
        $this->instructorIlmSessions    = [];
        $this->programYears             = [];
        $this->roles                    = [];
        $this->reports                  = [];
        $this->cohorts                  = [];
        $this->pendingUserUpdates       = [];
        $this->auditLogs                = [];
        $this->permissions              = [];
        $this->learnerIlmSessions       = [];
        $this->directedSchools          = [];
        $this->administeredSchools      = [];
        $this->administeredSessions     = [];
        $this->directedPrograms         = [];
    }
}
