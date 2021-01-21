<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;
use App\Entity\UserInterface;

/**
 * Class UserDTO
 * Data transfer object for a user
 * @IS\DTO("users")
 */
class UserDTO
{
    /**
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $id;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $lastName;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $firstName;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $middleName;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $displayName;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $phone;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $email;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $preferredEmail;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $addedViaIlios;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $enabled;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $campusId;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $otherId;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $examined;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $userSyncIgnore;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $icsFeedKey;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $reports;

    /**
     * @IS\Expose
     * @IS\Related("schools")
     * @IS\Type("string")
     */
    public int $school;

    /**
     * @IS\Expose
     * @IS\Related("authentications")
     * @IS\Type("string")
     */
    public ?int $authentication;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("courses")
     * @IS\Type("array<string>")
     */
    public array $directedCourses;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("courses")
     * @IS\Type("array<string>")
     */
    public array $administeredCourses;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("courses")
     * @IS\Type("array<string>")
     */
    public array $studentAdvisedCourses;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $learnerGroups;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("learnerGroups")
     * @IS\Type("array<string>")
     */
    public array $instructedLearnerGroups;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $instructorGroups;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("ilmSessions")
     * @IS\Type("array<string>")
     */
    public array $instructorIlmSessions;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("ilmSession")
     * @IS\Type("array<string>")
     */
    public array $learnerIlmSessions;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $offerings;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("offerings")
     * @IS\Type("array<string>")
     */
    public array $instructedOfferings;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $programYears;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("userRoles")
     * @IS\Type("array<string>")
     */
    public array $roles;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $cohorts;

    /**
     * @IS\Expose
     * @IS\Related("cohorts")
     * @IS\Type("string")
     */
    public ?int $primaryCohort;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $pendingUserUpdates;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $root;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("schools")
     * @IS\Type("array<string>")
     */
    public array $directedSchools;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("schools")
     * @IS\Type("array<string>")
     */
    public array $administeredSchools;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("sessions")
     * @IS\Type("array<string>")
     */
    public array $administeredSessions;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("sessions")
     * @IS\Type("array<string>")
     */
    public array $studentAdvisedSessions;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("programs")
     * @IS\Type("array<string>")
     */
    public array $directedPrograms;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("curriculumInventoryReports")
     * @IS\Type("array<string>")
     */
    public array $administeredCurriculumInventoryReports;

    /**
     * @var int[]
     */
    public array $auditLogs;

    /**
     * For index use, not public
     */
    public string $username;

    public function __construct(
        int $id,
        string $firstName,
        string $lastName,
        ?string $middleName,
        ?string $displayName,
        ?string $phone,
        string $email,
        ?string $preferredEmail,
        bool $addedViaIlios,
        bool $enabled,
        ?string $campusId,
        ?string $otherId,
        bool $examined,
        bool $userSyncIgnore,
        string $icsFeedKey,
        bool $root
    ) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->middleName = $middleName;
        $this->displayName = $displayName;
        $this->phone = $phone;
        $this->email = $email;
        $this->preferredEmail = $preferredEmail;
        $this->addedViaIlios = $addedViaIlios;
        $this->enabled = $enabled;
        $this->campusId = $campusId;
        $this->otherId = $otherId;
        $this->examined = $examined;
        $this->userSyncIgnore = $userSyncIgnore;
        $this->icsFeedKey = $icsFeedKey;
        $this->root = $root;

        $this->directedCourses = [];
        $this->administeredCourses = [];
        $this->studentAdvisedCourses = [];
        $this->studentAdvisedSessions = [];
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
        $this->learnerIlmSessions = [];
        $this->directedSchools = [];
        $this->administeredSchools = [];
        $this->administeredSessions = [];
        $this->directedPrograms = [];
        $this->administeredCurriculumInventoryReports = [];
    }
}
