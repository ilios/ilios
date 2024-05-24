<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Ignore;

#[IA\DTO('users')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "User",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "integer"
        ),
        new OA\Property(
            "lastName",
            description: "Last name",
            type: "string"
        ),
        new OA\Property(
            "firstName",
            description: "First name",
            type: "string"
        ),
        new OA\Property(
            "middleName",
            description: "Middle name",
            type: "string"
        ),
        new OA\Property(
            "displayName",
            description: "Display name",
            type: "string"
        ),
        new OA\Property(
            "phone",
            description: "Phone number",
            type: "string"
        ),
        new OA\Property(
            "email",
            description: "Email address",
            type: "string"
        ),
        new OA\Property(
            "preferredEmail",
            description: "Preferred email address",
            type: "string"
        ),
        new OA\Property(
            "pronouns",
            description: "Pronouns",
            type: "string"
        ),
        new OA\Property(
            "addedViaIlios",
            description: "Has been added via Ilios",
            type: "boolean"
        ),
        new OA\Property(
            "enabled",
            description: "Is enabled",
            type: "boolean"
        ),
        new OA\Property(
            "campusId",
            description: "Campus ID",
            type: "string"
        ),
        new OA\Property(
            "otherId",
            description: "Other ID",
            type: "string"
        ),
        new OA\Property(
            "examined",
            description: "Has been examined",
            type: "boolean"
        ),
        new OA\Property(
            "userSyncIgnore",
            description: "Is ignored from user sync",
            type: "boolean"
        ),
        new OA\Property(
            "root",
            description: "Is Root user",
            type: "boolean"
        ),
        new OA\Property(
            "icsFeedKey",
            description: "ICS feed key",
            type: "string"
        ),
        new OA\Property(
            "school",
            description: "School",
            type: "integer"
        ),
        new OA\Property(
            "authentication",
            description: "Authentication",
            type: "integer"
        ),
        new OA\Property(
            "primaryCohort",
            description: "Primary cohort",
            type: "integer"
        ),
        new OA\Property(
            "reports",
            description: "Reports",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "directedCourses",
            description: "Directed courses",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "administeredCourses",
            description: "Administered courses",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "studentAdvisedCourses",
            description: "Student-advised courses",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "learnerGroups",
            description: "Leaner groups",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "instructedLearnerGroups",
            description: "Instructed learner groups",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "instructorGroups",
            description: "Instructor groups",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "instructorIlmSessions",
            description: "Instructor ILM sessions",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "learnerIlmSessions",
            description: "Learner ILM sessions",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "offerings",
            description: "Offerings",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "instructedOfferings",
            description: "Instructed offerings",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "programYears",
            description: "Program years",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "roles",
            description: "Roles",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "cohort",
            description: "Cohort",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "pendingUserUpdates",
            description: "Pending user updates",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "directedSchools",
            description: "Directed schools",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "administeredSchools",
            description: "Administered schools",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "administeredSessions",
            description: "Administered sessions",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "studentAdvisedSessions",
            description: "Student-advised sessions",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "directedPrograms",
            description: "Directed programs",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "administeredCurriculumInventoryReports",
            description: "Administered curriculum inventory reports",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "sessionMaterialStatuses",
            description: "Session Material Statuses",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "auditLogs",
            description: "Audit logs",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
#[IA\FilterableBy('roles', IA\Type::INTEGERS)]
#[IA\FilterableBy('instructedCourses', IA\Type::INTEGERS)]
#[IA\FilterableBy('instructedSessions', IA\Type::INTEGERS)]
#[IA\FilterableBy('instructedSessionTypes', IA\Type::INTEGERS)]
#[IA\FilterableBy('instructedLearningMaterials', IA\Type::INTEGERS)]
#[IA\FilterableBy('learnerSessions', IA\Type::INTEGERS)]
#[IA\FilterableBy('schools', IA\Type::INTEGERS)]
#[IA\FilterableBy('instructedAcademicYears', IA\Type::INTEGERS)]
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
    #[IA\Type('string')]
    public ?string $pronouns;

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
    #[IA\Type('boolean')]
    public bool $root;


    #[IA\Expose]
    #[IA\Type('string')]
    public string $icsFeedKey;


    #[IA\Expose]
    #[IA\Related('schools')]
    #[IA\Type('integer')]
    public int $school;

    #[IA\Expose]
    #[IA\Related('authentications')]
    #[IA\Type('integer')]
    public ?int $authentication = null;

    #[IA\Expose]
    #[IA\Related('cohorts')]
    #[IA\Type('integer')]
    public ?int $primaryCohort = null;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $reports = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('courses')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $directedCourses = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('courses')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $administeredCourses = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('courses')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $studentAdvisedCourses = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $learnerGroups = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('learnerGroups')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $instructedLearnerGroups = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $instructorGroups = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('ilmSessions')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $instructorIlmSessions = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('ilmSessions')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $learnerIlmSessions = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $offerings = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('offerings')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $instructedOfferings = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $programYears = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('userRoles')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $roles = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $cohorts = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $pendingUserUpdates = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('schools')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $directedSchools = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('schools')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $administeredSchools = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('sessions')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $administeredSessions = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('sessions')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $studentAdvisedSessions = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('programs')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $directedPrograms = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('curriculumInventoryReports')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $administeredCurriculumInventoryReports = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('userSessionMaterialStatuses')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $sessionMaterialStatuses = [];

    /**
     * @var int[]
     */
    #[Ignore]
    public array $auditLogs = [];

    /**
     * For index use, not public
     */
    #[Ignore]
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
        ?string $pronouns,
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
        $this->pronouns = $pronouns;
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
