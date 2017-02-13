<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class CourseDTO
 * Data transfer object for a course
 * @package Ilios\CoreBundle\Entity\DTO

 */
class CourseDTO
{
    /**
     * @var int
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @IS\Type("string")
     */
    public $title;

    /**
     * @var int
     * @IS\Type("integer")
     */
    public $level;

    /**
     * @var int
     * @IS\Type("integer")
     */
    public $year;

    /**
     * @var \DateTime
     * @IS\Type("dateTime")
     */
    public $startDate;

    /**
     * @var \DateTime
     * @IS\Type("dateTime")
     */
    public $endDate;

    /**
     * @var string
     * @IS\Type("string")
     */
    public $externalId;

    /**
     * @var boolean
     * @IS\Type("boolean")
     */
    public $locked;

    /**
     * @var boolean
     * @IS\Type("boolean")
     */
    public $archived;

    /**
     * @var boolean
     * @IS\Type("boolean")
     */
    public $publishedAsTbd;

    /**
     * @var boolean
     * @IS\Type("boolean")
     */
    public $published;

    /**
     * @var int
     * @IS\Type("string")
     */
    public $clerkshipType;

    /**
     * @var int
     * @IS\Type("string")
     */
    public $school;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $directors;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $administrators;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $cohorts;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $terms;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $objectives;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $meshDescriptors;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $learningMaterials;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $sessions;

    /**
     * @var int
     * @IS\Type("string")
     */
    public $ancestor;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
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
