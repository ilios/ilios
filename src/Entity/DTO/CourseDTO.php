<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;
use DateTime;

/**
 * Class CourseDTO
 * Data transfer object for a course
 * @IS\DTO("courses")
 */
class CourseDTO
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
    public ?string $title;

    /**
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $level;

    /**
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $year;

    /**
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public DateTime $startDate;

    /**
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public DateTime $endDate;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $externalId;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $locked;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $archived;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $publishedAsTbd;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $published;

    /**
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\Related("courseClerkshipTypes")
     */
    public ?int $clerkshipType = null;

    /**
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\Related("schools")
     */
    public int $school;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     * @IS\Related("users")
     */
    public array $directors = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     * @IS\Related("users")
     */
    public array $administrators = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     * @IS\Related("users")
     */
    public array $studentAdvisors = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $cohorts = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $terms = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $courseObjectives = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $meshDescriptors = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("courseLearningMaterials")
     * @IS\Type("array<string>")
     */
    public array $learningMaterials = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $sessions = [];

    /**
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\Related("courses")
     */
    public ?int $ancestor = null;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("courses")
     * @IS\Type("array<string>")
     */
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
