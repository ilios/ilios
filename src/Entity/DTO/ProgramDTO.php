<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class ProgramDTO
 * Data transfer object for a Program
 * @IS\DTO("programs")
 */
class ProgramDTO
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
    public ?string $shortTitle;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public int $duration;

    /**
     * @IS\Expose
     * @IS\Related("schools")
     * @IS\Type("integer")
     */
    public int $school;

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
    public array $curriculumInventoryReports = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("array<string>")
     */
    public array $directors = [];


    public function __construct(
        int $id,
        string $title,
        ?string $shortTitle,
        int $duration
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->shortTitle = $shortTitle;
        $this->duration = $duration;
    }
}
