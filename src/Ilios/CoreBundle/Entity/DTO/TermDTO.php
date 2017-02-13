<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class TermDTO
 * Data transfer object for a session.
 * @package Ilios\CoreBundle\Entity\DTO

 */
class TermDTO
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
     *
     * @var string
     * @IS\Type("string")
     *
     */
    public $description;

    /**
     * @var int
     * @IS\Type("string")
     */
    public $parent;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $children;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $courses;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $programYears;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $sessions;

    /**
     * @var int
     * @IS\Type("string")
     */
    public $vocabulary;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $aamcResourceTypes;

    /**
     * @var boolean
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
