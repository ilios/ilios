<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class CompetencyDTO
 * Data transfer object for a competency
 *
 * @IS\DTO
 */
class CompetencyDTO
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
     *
     */
    public $title;

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
    public $objectives;

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
    public $aamcPcrses;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $programYears;

    /**
     * @var boolean
     * @IS\Expose
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
