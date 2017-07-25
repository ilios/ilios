<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class CourseDTO
 * Data transfer object for a course
 *
 * @IS\DTO
 */
class CourseDTO
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
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $level;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $year;

    /**
     * @IS\Expose
     * @var \DateTime
     * @IS\Type("dateTime")
     */
    public $startDate;

    /**
     * @var \DateTime
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public $endDate;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $externalId;

    /**
     * @var boolean
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $locked;

    /**
     * @var boolean
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $archived;

    /**
     * @var boolean
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $publishedAsTbd;

    /**
     * @var boolean
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $published;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $clerkshipType;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $school;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $directors;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $administrators;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $cohorts;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $terms;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $objectives;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $meshDescriptors;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $learningMaterials;

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
    public $ancestor;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $descendants;

    public function __construct(
        $id,
        $title,
        $level,
        $year,
        $startDate,
        $endDate,
        $externalId,
        $locked,
        $archived,
        $publishedAsTbd,
        $published
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->level = $level;
        $this->year = $year;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->externalId = $externalId;
        $this->locked = $locked;
        $this->archived = $archived;
        $this->publishedAsTbd = $publishedAsTbd;
        $this->published = $published;

        $this->directors = [];
        $this->administrators = [];
        $this->cohorts = [];
        $this->terms = [];
        $this->objectives = [];
        $this->meshDescriptors = [];
        $this->learningMaterials = [];
        $this->sessions = [];
        $this->descendants = [];
    }
}
