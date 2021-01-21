<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class CourseObjectiveDTO
 *
 * @IS\DTO("courseObjectives")
 */
class CourseObjectiveDTO
{
    /**
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $id;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $title;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $active;

    /**
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $position;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $terms;

    /**
     * @IS\Expose
     * @IS\Related("courses")
     * @IS\Type("integer")
     */
    public int $course;

    /**
     * Needed for Voting, not exposed in the API
     */
    public int $school;

    /**
     * Needed for Voting, not exposed in the API
     */
    public bool $courseIsLocked;

    /**
     * Needed for Voting, not exposed in the API
     */
    public bool $courseIsArchived;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("programYearObjectives")
     * @IS\Type("array<string>")
     */
    public array $programYearObjectives;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("sessionObjectives")
     * @IS\Type("array<string>")
     */
    public array $sessionObjectives;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $meshDescriptors;

    /**
     * @IS\Expose
     * @IS\Related("courseObjectives")
     * @IS\Type("string")
     */
    public ?int $ancestor;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("courseObjectives")
     * @IS\Type("array<string>")
     */
    public array $descendants;

    public function __construct(int $id, string $title, int $position, bool $active)
    {
        $this->id = $id;
        $this->title = $title;
        $this->position = $position;
        $this->active = $active;

        $this->terms = [];
        $this->meshDescriptors = [];
        $this->programYearObjectives = [];
        $this->sessionObjectives = [];
        $this->descendants = [];
    }
}
