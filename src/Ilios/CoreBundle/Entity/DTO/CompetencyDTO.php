<?php

namespace Ilios\CoreBundle\Entity\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * Class CompetencyDTO
 * Data transfer object for a competency
 * @package Ilios\CoreBundle\Entity\DTO

 */
class CompetencyDTO
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
     * @var int
     * @JMS\Type("string")
     */
    public $school;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $objectives;

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
     * @JMS\SerializedName("aamcPcrses")
     */
    public $aamcPcrses;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("programYears")
     */
    public $programYears;

    /**
     * @var boolean
     * @JMS\Type("boolean")
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
