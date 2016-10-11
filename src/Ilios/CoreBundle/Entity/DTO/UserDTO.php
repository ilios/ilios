<?php

namespace Ilios\CoreBundle\Entity\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * Class UserDTO
 * Data transfer object for a user
 * @package Ilios\CoreBundle\Entity\DTO

 */
class UserDTO
{
    /**
     * @var int
     * @JMS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("lastName")
     */
    public $lastName;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("firstName")
     */
    public $firstName;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("middleName")
     */
    public $middleName;

    /**
     * @var string
     * @JMS\Type("string")
     */
    public $phone;

    /**
     * @var string
     * @JMS\Type("string")
     */
    public $email;

    /**
     * @var boolean
     * @JMS\Type("boolean")
     */
    public $enabled;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("campusId")
     */
    public $campusId;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("otherId")
     */
    public $otherId;

    /**
     * @var boolean
     * @JMS\Type("boolean")
     * @JMS\SerializedName("userSyncIgnore")
     */
    public $userSyncIgnore;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("icsFeedKey")
     */
    public $icsFeedKey;

    /**
     * @var array
     * @JMS\Type("array<string>")
     */
    public $reminders;

    /**
     * @var array
     * @JMS\Type("array<string>")
     */
    public $reports;

    /**
     * @var int
     * @JMS\Type("string")
     */
    public $school;

    /**
     * @var int
     * @JMS\Type("string")
     */
    public $authentication;

    /**
     * @var array
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("directedCourses")
     */
    public $directedCourses;

    /**
     * @var array
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("administeredCourses")
     */
    public $administeredCourses;

    /**
     * @var array
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("learnerGroups")
     */
    public $learnerGroups;

    /**
     * @var array
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("instructedLearnerGroups")
     */
    public $instructedLearnerGroups;

    /**
     * @var array
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("instructorGroups")
     */
    public $instructorGroups;

    /**
     * @var array
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("instructorIlmSessions")
     */
    public $instructorIlmSessions;

    /**
     * @var array
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("learnerIlmSessions")
     */
    public $learnerIlmSessions;

    /**
     * @var array
     * @JMS\Type("array<string>")
     */
    public $offerings;

    /**
     * @var array
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("instructedOfferings")
     */
    public $instructedOfferings;

    /**
     * @var array
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("programYears")
     */
    public $programYears;

    /**
     * @var array
     * @JMS\Type("array<string>")
     */
    public $roles;

    /**
     * @var array
     * @JMS\Type("array<string>")
     */
    public $cohorts;

    /**
     * @var int
     * @JMS\Type("string")
     * @JMS\SerializedName("primaryCohort")
     */
    public $primaryCohort;

    /**
     * @var array
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("pendingUserUpdates")
     */
    public $pendingUserUpdates;

    /**
     * @var boolean
     * @JMS\Type("boolean")

     */
    public $root;

    /**
     * @var array
     * @JMS\Type("array<string>")
     **/
    public $permissions;

    /**
     * @var array
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("directedSchools")
     */
    public $directedSchools;

    /**
     * @var array
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("administeredSchools")
     */
    public $administeredSchools;

    /**
     * @var array
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("administeredSessions")
     */
    public $administeredSessions;

    /**
     * @var array
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("directedPrograms")
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
