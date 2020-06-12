<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class ProgramYearObjectiveDTO
 *
 * @IS\DTO("programYearObjectives")
 */
class ProgramYearObjectiveDTO
{
    /**
     * @var int
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     *
     */
    public $title;

    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("boolean")
     *
     */
    public $active;

    /**
     * @var int
     * @IS\Expose
     * @IS\Related("competencies")
     * @IS\Type("string")
     *
     */
    public $competency;

    /**
     * @var int
     *
     * @IS\Related("objectives")
     * @IS\Type("integer")
     */
    public $objective;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     *
     */
    public $position;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $terms;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Related("programYears")
     * @IS\Type("integer")
     */
    public $programYear;

    /**
     * Needed for Voting, not exposed in the API
     * @var bool
     *
     * @IS\Type("boolean")
     */
    public $programYearIsLocked;

    /**
     * Needed for Voting, not exposed in the API
     * @var bool
     *
     * @IS\Type("boolean")
     */
    public $programYearIsArchived;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("courseObjectives")
     * @IS\Type("array<string>")
     *
     */
    public $children;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     *
     */
    public $meshDescriptors;

    /**
     * @var int
     * @IS\Expose
     * @IS\Related("programYearObjectives")
     * @IS\Type("string")
     *
     */
    public $ancestor;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("programYearObjectives")
     * @IS\Type("array<string>")
     *
     */
    public $descendants;


    /**
     * Constructor
     * @param int $id
     * @param string $title
     * @param int $position
     * @param bool $active
     */
    public function __construct($id, $title, $position, $active)
    {
        $this->id = $id;
        $this->title = $title;
        $this->position = $position;
        $this->active = $active;

        $this->terms = [];
        $this->meshDescriptors = [];
        $this->children = [];
        $this->descendants = [];
    }
}
