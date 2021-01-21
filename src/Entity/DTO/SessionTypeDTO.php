<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class SessionTypeDTO
 * Data transfer object for a session type
 *
 * @IS\DTO("sessionTypes")
 */
class SessionTypeDTO
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
    public string $calendarColor;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
    */
    public bool $active;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
    */
    public bool $assessment;

    /**
     * @IS\Expose
     * @IS\Related("assessmentOptions")
     * @IS\Type("entity")
     */
    public int $assessmentOption;

    /**
     * @IS\Expose
     * @IS\Related("schools")
     * @IS\Type("entity")
     */
    public int $school;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $aamcMethods;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $sessions;

    public function __construct(int $id, string $title, string $calendarColor, bool $assessment, bool $active)
    {
        $this->id = $id;
        $this->title = $title;
        $this->calendarColor = $calendarColor;
        $this->assessment = $assessment;
        $this->active = $active;

        $this->aamcMethods = [];
        $this->sessions = [];
    }
}
