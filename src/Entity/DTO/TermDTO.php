<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class TermDTO
 * Data transfer object for a session.
 *
 * @IS\DTO("terms")
 */
class TermDTO
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
     * @IS\Type("string")
     */
    public ?string $description;

    /**
     * @IS\Expose
     * @IS\Related("terms")
     * @IS\Type("integer")
     */
    public ?int $parent = null;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("terms")
     * @IS\Type("array<string>")
     */
    public array $children = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $courses = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $programYears = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $sessions = [];

    /**
     * @IS\Expose
     * @IS\Related("vocabularies")
     * @IS\Type("integer")
     */
    public int $vocabulary;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $aamcResourceTypes = [];

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $active;

    /**
     * For Voter use, not public
     */
    public int $school;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $sessionObjectives = [];

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
    public array $programYearObjectives = [];

    public function __construct(
        int $id,
        string $title,
        ?string $description,
        bool $active
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->active = $active;
    }
}
