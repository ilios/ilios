<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class UserDTO
 * Data transfer object for a user
 * @IS\DTO
 */
class UserDTO
{
    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $lastName;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $firstName;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $middleName;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $phone;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $email;

    /**
     * @var boolean
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $addedViaIlios;

    /**
     * @var boolean
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $enabled;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $campusId;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $otherId;

    /**
     * @var boolean
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $examined;

    /**
     * @var boolean
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $userSyncIgnore;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $icsFeedKey;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $reminders;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $reports;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $school;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $authentication;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $directedCourses;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $administeredCourses;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $learnerGroups;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $instructedLearnerGroups;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $instructorGroups;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $instructorIlmSessions;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $learnerIlmSessions;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $offerings;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $instructedOfferings;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $programYears;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $roles;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $cohorts;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $primaryCohort;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $pendingUserUpdates;

    /**
     * @var boolean
     * @IS\Expose
     * @IS\Type("boolean")

     */
    public $root;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     **/
    public $permissions;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $directedSchools;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $administeredSchools;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $administeredSessions;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $directedPrograms;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $administeredCurriculumInventoryReports;

    public function __construct(
        $id,
        $firstName,
        $lastName,
        $middleName,
        $phone,
        $email,
        $addedViaIlios,
        $enabled,
        $campusId,
        $otherId,
        $examined,
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
        $this->addedViaIlios = $addedViaIlios;
        $this->enabled = $enabled;
        $this->campusId = $campusId;
        $this->otherId = $otherId;
        $this->examined = $examined;
        $this->userSyncIgnore = $userSyncIgnore;
        $this->icsFeedKey = $icsFeedKey;
        $this->root = $root;

        $this->reminders = [];
        $this->directedCourses = [];
        $this->administeredCourses = [];
        $this->learnerGroups = [];
        $this->instructedLearnerGroups = [];
        $this->instructorGroups = [];
        $this->offerings = [];
        $this->instructedOfferings = [];
        $this->instructorIlmSessions = [];
        $this->programYears = [];
        $this->roles = [];
        $this->reports = [];
        $this->cohorts = [];
        $this->pendingUserUpdates = [];
        $this->auditLogs = [];
        $this->permissions = [];
        $this->learnerIlmSessions = [];
        $this->directedSchools = [];
        $this->administeredSchools = [];
        $this->administeredSessions = [];
        $this->directedPrograms = [];
        $this->administeredCurriculumInventoryReports = [];
    }
}
