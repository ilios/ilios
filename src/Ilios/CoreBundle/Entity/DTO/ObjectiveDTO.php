<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

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


    public function __construct(
        $id,
        $title,
        $position
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->position = $position;

        $this->courses = [];
        $this->programYears = [];
        $this->sessions = [];
        $this->parents = [];
        $this->children = [];
        $this->meshDescriptors = [];
        $this->descendants = [];
    }
}
