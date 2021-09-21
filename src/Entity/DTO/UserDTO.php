<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;

/**
 * Class UserDTO
 * Data transfer object for a user
 */
#[IA\DTO('users')]
class UserDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $lastName;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $firstName;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $middleName;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $displayName;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $phone;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $email;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $preferredEmail;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $addedViaIlios;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $enabled;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $campusId;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $otherId;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $examined;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $userSyncIgnore;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $icsFeedKey;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $reports = [];

    #[IA\Expose]
    #[IA\Related('schools')]
    #[IA\Type('integer')]
    public int $school;

    #[IA\Expose]
    #[IA\Related('authentications')]
    #[IA\Type('integer')]
    public ?int $authentication = null;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('courses')]
    #[IA\Type('array<string>')]
    public array $directedCourses = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('courses')]
    #[IA\Type('array<string>')]
    public array $administeredCourses = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('courses')]
    #[IA\Type('array<string>')]
    public array $studentAdvisedCourses = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $learnerGroups = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('learnerGroups')]
    #[IA\Type('array<string>')]
    public array $instructedLearnerGroups = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $instructorGroups = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('ilmSessions')]
    #[IA\Type('array<string>')]
    public array $instructorIlmSessions = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('ilmSessions')]
    #[IA\Type('array<string>')]
    public array $learnerIlmSessions = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $offerings = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('offerings')]
    #[IA\Type('array<string>')]
    public array $instructedOfferings = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $programYears = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('userRoles')]
    #[IA\Type('array<string>')]
    public array $roles = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $cohorts = [];

    #[IA\Expose]
    #[IA\Related('cohorts')]
    #[IA\Type('integer')]
    public ?int $primaryCohort = null;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $pendingUserUpdates = [];

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $root;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('schools')]
    #[IA\Type('array<string>')]
    public array $directedSchools = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('schools')]
    #[IA\Type('array<string>')]
    public array $administeredSchools = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('sessions')]
    #[IA\Type('array<string>')]
    public array $administeredSessions = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('sessions')]
    #[IA\Type('array<string>')]
    public array $studentAdvisedSessions = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('programs')]
    #[IA\Type('array<string>')]
    public array $directedPrograms = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('curriculumInventoryReports')]
    #[IA\Type('array<string>')]
    public array $administeredCurriculumInventoryReports = [];

    /**
     * @var int[]
     */
    public array $auditLogs = [];

    /**
     * For index use, not public
     */
    public ?string $username = null;

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
    }
}
