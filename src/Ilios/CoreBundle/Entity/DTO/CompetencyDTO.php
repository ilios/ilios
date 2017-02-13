<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class CompetencyDTO
 * Data transfer object for a competency
 * @package Ilios\CoreBundle\Entity\DTO

 */
class CompetencyDTO
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
     * @var int
     * @IS\Type("string")
     */
    public $school;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $objectives;

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
    public $aamcPcrses;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $programYears;

    /**
     * @var boolean
     * @IS\Type("boolean")
     *
     */
    public $active;

    public function __construct(
        $id,
        $title,
        $active
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->active = $active;

        $this->objectives = [];
        $this->children = [];
        $this->aamcPcrses = [];
        $this->programYears = [];
    }
}
