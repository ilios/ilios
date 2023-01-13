<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use DateTime;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Ignore;

#[IA\DTO('sessions')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "School",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "integer"
        ),
        new OA\Property(
            "title",
            description: "Title",
            type: "string"
        ),
        new OA\Property(
            "attireRequired",
            description: "Is special attire required",
            type: "boolean"
        ),
        new OA\Property(
            "equipmentRequired",
            description: "Is equipment required",
            type: "boolean"
        ),
        new OA\Property(
            "supplemental",
            description: "Is supplemental",
            type: "boolean"
        ),
        new OA\Property(
            "attendanceRequired",
            description: "Is attendance required",
            type: "boolean"
        ),
        new OA\Property(
            "publishedAsTbd",
            description: "Is partially published",
            type: "boolean"
        ),
        new OA\Property(
            "published",
            description: "Is published",
            type: "boolean"
        ),
        new OA\Property(
            "instructionalNotes",
            description: "Instructional notes",
            type: "string"
        ),
        new OA\Property(
            "updatedAt",
            description: "Updated at",
            type: "string",
            format: "date-time"
        ),
        new OA\Property(
            "description",
            description: "Description",
            type: "string"
        ),
        new OA\Property(
            "sessionType",
            description: "Session type",
            type: "integer"
        ),
        new OA\Property(
            "course",
            description: "Course",
            type: "integer"
        ),
        new OA\Property(
            "ilmSession",
            description: "ILM session",
            type: "integer"
        ),
        new OA\Property(
            "postrequisite",
            description: "Postrequisite",
            type: "integer"
        ),
        new OA\Property(
            "terms",
            description: "Vocabulary terms",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "sessionObjectives",
            description: "Session objectives",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "meshDescriptors",
            description: "MeSH descriptors",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "learningMaterials",
            description: "Session learning materials",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "administrators",
            description: "Administrators",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "studentAdvisors",
            description: "Student advisors",
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
            "prerequisites",
            description: "Prerequisites",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
#[IA\FilterableBy('programs', 'array<integer>')]
#[IA\FilterableBy('instructors', 'array<integer>')]
#[IA\FilterableBy('instructorGroups', 'array<integer>')]
#[IA\FilterableBy('competencies', 'array<integer>')]
#[IA\FilterableBy('schools', 'array<integer>')]
#[IA\FilterableBy('courses', 'array<integer>')]
#[IA\FilterableBy('sessionTypes', 'array<integer>')]
class SessionDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $title;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public ?bool $attireRequired;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public ?bool $equipmentRequired;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public ?bool $supplemental;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public ?bool $attendanceRequired;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $publishedAsTbd;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $published;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $instructionalNotes;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $updatedAt;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $description;

    #[IA\Expose]
    #[IA\Related('sessionTypes')]
    #[IA\Type('integer')]
    public int $sessionType;

    #[IA\Expose]
    #[IA\Related('courses')]
    #[IA\Type('integer')]
    public int $course;

    #[IA\Expose]
    #[IA\Related('ilmSessions')]
    #[IA\Type('integer')]
    public ?int $ilmSession = null;

    #[IA\Expose]
    #[IA\Related('sessions')]
    #[IA\Type('integer')]
    public ?int $postrequisite = null;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<integer>')]
    public array $terms = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<integer>')]
    public array $sessionObjectives = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $meshDescriptors = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('sessionLearningMaterials')]
    #[IA\Type('array<integer>')]
    public array $learningMaterials = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type('array<integer>')]
    public array $administrators = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type('array<integer>')]
    public array $studentAdvisors = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<integer>')]
    public array $offerings = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('sessions')]
    #[IA\Type('array<integer>')]
    public array $prerequisites = [];

    /**
     * For Voter use, not public
     */
    #[Ignore]
    public int $school;

    public function __construct(
        int $id,
        ?string $title,
        ?string $description,
        ?bool $attireRequired,
        ?bool $equipmentRequired,
        ?bool $supplemental,
        ?bool $attendanceRequired,
        bool $publishedAsTbd,
        bool $published,
        ?string $instructionalNotes,
        DateTime $updatedAt
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->attireRequired = $attireRequired;
        $this->equipmentRequired = $equipmentRequired;
        $this->supplemental = $supplemental;
        $this->attendanceRequired = $attendanceRequired;
        $this->publishedAsTbd = $publishedAsTbd;
        $this->published = $published;
        $this->instructionalNotes = $instructionalNotes;
        $this->updatedAt = $updatedAt;
    }
}
