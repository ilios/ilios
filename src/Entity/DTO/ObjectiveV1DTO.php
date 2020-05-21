<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;
use App\Entity\Manager\CourseObjectiveManager;
use App\Entity\Manager\ProgramYearObjectiveManager;
use App\Entity\Manager\SessionObjectiveManager;
use App\Entity\DTO\ObjectiveDTO as CurrentDTO;

/**
 * Class ObjectiveDTO
 * Data transfer object for a Objective
 *
 * @IS\DTO
 */
class ObjectiveV1DTO
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
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     *
     */
    public $competency;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     *
     */
    public $courses;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     *
     */
    public $programYears;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     *
     */
    public $sessions;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     *
     */
    public $parents;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
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
     * @IS\Type("string")
     *
     */
    public $ancestor;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     *
     */
    public $descendants;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $position;

    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("integer")
     *
     */
    public $active;


    public function __construct($id, $title, $active)
    {
        $this->id = $id;
        $this->title = $title;
        $this->active = $active;
        $this->position = 0;

        $this->courses = [];
        $this->sessions = [];
        $this->programYears = [];
        $this->parents = [];
        $this->children = [];
        $this->meshDescriptors = [];
        $this->descendants = [];
    }
}
