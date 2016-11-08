<?php

namespace Ilios\CoreBundle\Entity\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * Class ObjectiveDTO
 * Data transfer object for a Objective
 * @package Ilios\CoreBundle\Entity\DTO

 */
class ObjectiveDTO
{
    /**
     * @var int
     * @JMS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @JMS\Type("string")
     *
     */
    public $title;

    /**
     * @var integer
     * @JMS\Type("string")
     *
     */
    public $competency;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("courses")
     *
     */
    public $courses;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("programYears")
     *
     */
    public $programYears;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     *
     */
    public $sessions;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     *
     */
    public $parents;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     *
     */
    public $children;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("meshDescriptors")
     *
     */
    public $meshDescriptors;

    /**
     * @var integer
     * @JMS\Type("string")
     *
     */
    public $ancestor;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     *
     */
    public $descendants;


    public function __construct(
        $id,
        $title
    ) {
        $this->id = $id;
        $this->title = $title;

        $this->courses = [];
        $this->programYears = [];
        $this->sessions = [];
        $this->parents = [];
        $this->children = [];
        $this->meshDescriptors = [];
        $this->descendants = [];
    }
}
