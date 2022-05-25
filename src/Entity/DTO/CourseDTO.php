<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use DateTime;
use OpenApi\Attributes as OA;

#[IA\DTO('courses')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "Course",
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
            "level",
            description: "Level",
            type: "integer"
        ),
        new OA\Property(
            "year",
            description: "Year",
            type: "integer"
        ),
        new OA\Property(
            "startDate",
            description: "Start date",
            type: "string",
            format: "date-time"
        ),
        new OA\Property(
            "endDate",
            description: "End date",
            type: "string",
            format: "date-time"
        ),
        new OA\Property(
            "externalId",
            description: "External ID",
            type: "string"
        ),
        new OA\Property(
            "locked",
            description: "Is locked",
            type: "boolean"
        ),
        new OA\Property(
            "archived",
            description: "Is archived",
            type: "boolean"
        ),
        new OA\Property(
            "publishedAsTbd",
            description: "Is published",
            type: "boolean"
        ),
        new OA\Property(
            "published",
            description: "Is fully published",
            type: "boolean"
        ),
        new OA\Property(
            "clerkshipType",
            description: "Clerkship type",
            type: "integer"
        ),
        new OA\Property(
            "school",
            description: "School",
            type: "integer"
        ),
        new OA\Property(
            "ancestor",
            description: "Ancestor",
            type: "integer"
        ),
        new OA\Property(
            "directors",
            description: "Directors",
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
            "cohorts",
            description: "Cohorts",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "terms",
            description: "Vocabulary terms",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "courseObjectives",
            description: "Course objectives",
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
            description: "Course learning materials",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "sessions",
            description: "Sessions",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "descendants",
            description: "Descendants",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
class CourseDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $title;

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $level;

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $year;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $startDate;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $endDate;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $externalId;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $locked;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $archived;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $publishedAsTbd;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $published;

    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\Related('courseClerkshipTypes')]
    public ?int $clerkshipType = null;

    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\Related('schools')]
    public int $school;

    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\Related('courses')]
    public ?int $ancestor = null;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Type('array<string>')]
    #[IA\Related('users')]
    public array $directors = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Type('array<string>')]
    #[IA\Related('users')]
    public array $administrators = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Type('array<string>')]
    #[IA\Related('users')]
    public array $studentAdvisors = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $cohorts = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $terms = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $courseObjectives = [];

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
    #[IA\Related('courseLearningMaterials')]
    #[IA\Type('array<string>')]
    public array $learningMaterials = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $sessions = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('courses')]
    #[IA\Type('array<string>')]
    public array $descendants = [];

    public function __construct(
        int $id,
        ?string $title,
        int $level,
        int $year,
        DateTime $startDate,
        DateTime $endDate,
        ?string $externalId,
        bool $locked,
        bool $archived,
        bool $publishedAsTbd,
        bool $published
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->level = $level;
        $this->year = $year;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->externalId = $externalId;
        $this->locked = $locked;
        $this->archived = $archived;
        $this->publishedAsTbd = $publishedAsTbd;
        $this->published = $published;
    }
}
