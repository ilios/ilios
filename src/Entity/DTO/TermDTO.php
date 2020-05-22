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
     * @IS\Related("terms")
     * @IS\Type("string")
     */
    public $parent;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("terms")
     * @IS\Type("array<string>")
     */
    public $children;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $courses;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $programYears;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $sessions;

    /**
     * @var int
     * @IS\Expose
     * @IS\Related("vocabularies")
     * @IS\Type("string")
     */
    public $vocabulary;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $aamcResourceTypes;

    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $active;

    /**
     * For Voter use, not public
     * @var int
     */
    public $school;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $sessionObjectives;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $courseObjectives;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $programYearObjectives;

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
        $this->sessionObjectives = [];
        $this->courseObjectives = [];
        $this->programYearObjectives = [];
    }
}
