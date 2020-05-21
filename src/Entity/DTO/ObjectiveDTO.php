<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class ObjectiveDTO
 * Data transfer object for a Objective
 *
 * @IS\DTO("objectives")
 */
class ObjectiveDTO
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
    public $courseObjectives;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     *
     */
    public $programYearObjectives;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     *
     */
    public $sessionObjectives;

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
     * @deprecated
     *
     */
    public $position;

    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("integer")
     *
     */
    public $active;


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

        $this->courseObjectives = [];
        $this->programYearObjectives = [];
        $this->sessionObjectives = [];
        $this->parents = [];
        $this->children = [];
        $this->meshDescriptors = [];
        $this->descendants = [];
    }
}
