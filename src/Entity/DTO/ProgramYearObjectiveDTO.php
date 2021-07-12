<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class ProgramYearObjectiveDTO
 * @IS\DTO("programYearObjectives")
 */
class ProgramYearObjectiveDTO
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
    public string $title;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $active;

    /**
     * @IS\Expose
     * @IS\Related("competencies")
     * @IS\Type("integer")
     */
    public ?int $competency = null;

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
    public array $terms = [];

    /**
     * @IS\Expose
     * @IS\Related("programYears")
     * @IS\Type("integer")
     */
    public int $programYear;

    /**
     * Needed for Voting, not exposed in the API
     * @IS\Type("boolean")
     */
    public bool $programYearIsLocked;

    /**
     * Needed for Voting, not exposed in the API
     * @IS\Type("boolean")
     */
    public bool $programYearIsArchived;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("courseObjectives")
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
     * @IS\Expose
     * @IS\Related("programYearObjectives")
     * @IS\Type("integer")
     */
    public ?int $ancestor = null;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("programYearObjectives")
     * @IS\Type("array<string>")
     */
    public array $descendants = [];

    public function __construct(int $id, string $title, int $position, bool $active)
    {
        $this->id = $id;
        $this->title = $title;
        $this->position = $position;
        $this->active = $active;
    }
}
