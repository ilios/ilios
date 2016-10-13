<?php

namespace Ilios\CoreBundle\Entity\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * Class CourseDTO
 * Data transfer object for a course
 * @package Ilios\CoreBundle\Entity\DTO

 */
class CourseDTO
{
    /**
     * @var int
     * @JMS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @JMS\Type("string")
     */
    public $title;

    /**
     * @var int
     * @JMS\Type("integer")
     */
    public $level;

    /**
     * @var int
     * @JMS\Type("integer")
     */
    public $year;

    /**
     * @var \DateTime
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("startDate")
     */
    public $startDate;

    /**
     * @var \DateTime
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("endDate")
     */
    public $endDate;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("externalId")
     */
    public $externalId;

    /**
     * @var boolean
     * @JMS\Type("boolean")
     */
    public $locked;

    /**
     * @var boolean
     * @JMS\Type("boolean")
     */
    public $archived;

    /**
     * @var boolean
     * @JMS\Type("boolean")
     * @JMS\SerializedName("publishedAsTbd")
     */
    public $publishedAsTbd;

    /**
     * @var boolean
     * @JMS\Type("boolean")
     */
    public $published;

    /**
     * @var int
     * @JMS\Type("string")
     * @JMS\SerializedName("clerkshipType")
     */
    public $clerkshipType;

    /**
     * @var int
     * @JMS\Type("string")
     * @JMS\SerializedName("school")
     */
    public $school;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $directors;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $administrators;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $cohorts;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $terms;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $objectives;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("meshDescriptors")
     */
    public $meshDescriptors;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("learningMaterials")
     */
    public $learningMaterials;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $sessions;

    /**
     * @var int
     * @JMS\Type("string")
     */
    public $ancestor;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
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
