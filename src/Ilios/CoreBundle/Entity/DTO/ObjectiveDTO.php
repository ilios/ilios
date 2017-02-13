<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class ObjectiveDTO
 * Data transfer object for a Objective
 * @package Ilios\CoreBundle\Entity\DTO

 */
class ObjectiveDTO
{
    /**
     * @var int
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @IS\Type("string")
     *
     */
    public $title;

    /**
     * @var integer
     * @IS\Type("string")
     *
     */
    public $competency;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     *
     */
    public $courses;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     *
     */
    public $programYears;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     *
     */
    public $sessions;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     *
     */
    public $parents;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     *
     */
    public $children;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     *
     */
    public $meshDescriptors;

    /**
     * @var integer
     * @IS\Type("string")
     *
     */
    public $ancestor;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
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
