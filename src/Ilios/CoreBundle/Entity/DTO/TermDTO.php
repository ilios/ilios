<?php

namespace Ilios\CoreBundle\Entity\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * Class TermDTO
 * Data transfer object for a session.
 * @package Ilios\CoreBundle\Entity\DTO

 */
class TermDTO
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
     *
     * @var string
     * @JMS\Type("string")
     *
     */
    public $description;

    /**
     * @var int
     * @JMS\Type("string")
     */
    public $parent;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $children;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $courses;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("programYears")
     */
    public $programYears;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $sessions;

    /**
     * @var int
     * @JMS\Type("string")
     * @JMS\SerializedName("vocabulary")
     */
    public $vocabulary;

    public function __construct(
        $id,
        $title,
        $description
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;

        $this->children = [];
        $this->courses = [];
        $this->programYears = [];
        $this->sessions = [];
    }
}
