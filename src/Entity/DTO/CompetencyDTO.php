<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class CompetencyDTO
 * Data transfer object for a competency
 *
 * @IS\DTO("competencies")
 */
class CompetencyDTO
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
     * @IS\Related("schools")
     * @IS\Type("string")
     */
    public int $school;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("programYearObjectives")
     * @IS\Type("array<string>")
     */
    public array $programYearObjectives;

    /**
     * @IS\Expose
     * @IS\Related("competencies")
     * @IS\Type("string")
     */
    public ?int $parent;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("competencies")
     * @IS\Type("array<string>")
     */
    public array $children;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $aamcPcrses;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $programYears;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     *
     */
    public bool $active;

    public function __construct(
        int $id,
        string $title,
        bool $active
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->active = $active;

        $this->programYearObjectives = [];
        $this->children = [];
        $this->aamcPcrses = [];
        $this->programYears = [];
    }
}
