<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class TermDTO
 * Data transfer object for a session.
 *
 * @IS\DTO
 */
class TermDTO
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
     */
    public $title;

    /**
     *
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     *
     */
    public $description;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $parent;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $children;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $courses;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $programYears;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $sessions;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $vocabulary;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $aamcResourceTypes;

    /**
     * @var boolean
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $active;

    public function __construct(
        $id,
        $title,
        $description,
        $active
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->active = $active;

        $this->children = [];
        $this->courses = [];
        $this->programYears = [];
        $this->sessions = [];
        $this->aamcResourceTypes = [];
    }
}
