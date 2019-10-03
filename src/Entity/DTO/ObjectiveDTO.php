<?php

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class ObjectiveDTO
 * Data transfer object for a Objective
 *
 * @IS\DTO
 */
class ObjectiveDTO
{
    /**
     * @var int
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
     * @var integer
     * @IS\Expose
     * @IS\Type("string")
     *
     */
    public $competency;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     *
     */
    public $courses;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     *
     */
    public $programYears;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     *
     */
    public $sessions;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     *
     */
    public $parents;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     *
     */
    public $children;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     *
     */
    public $meshDescriptors;

    /**
     * @var integer
     * @IS\Expose
     * @IS\Type("string")
     *
     */
    public $ancestor;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     *
     */
    public $descendants;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     *
     */
    public $position;

    /**
     * @var boolean
     * @IS\Expose
     * @IS\Type("integer")
     *
     */
    public $active;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     *
     */
    public $terms;


    public function __construct(
        $id,
        $title,
        $position,
        $active
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->position = $position;
        $this->active = $active;

        $this->courses = [];
        $this->programYears = [];
        $this->sessions = [];
        $this->parents = [];
        $this->children = [];
        $this->meshDescriptors = [];
        $this->descendants = [];
        $this->terms = [];
    }
}
