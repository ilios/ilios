<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use DateTime;

/**
 * Class CourseDTO
 * Data transfer object for a course
 */
#[IA\DTO('courses')]
#[IA\ExposeGraphQL]
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

    /**
     * @var string
     */
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

    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\Related('courses')]
    public ?int $ancestor = null;

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
