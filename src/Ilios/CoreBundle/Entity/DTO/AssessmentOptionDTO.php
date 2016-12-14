<?php

namespace Ilios\CoreBundle\Entity\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * Class AssessmentOptionDTO
 * Data transfer object for an assessmentOption
 * @package Ilios\CoreBundle\Entity\DTO
 */
class AssessmentOptionDTO
{
    /**
     * @var int
     * @JMS\Type("integer")
     */
    public $id;

    /**
     * @var int
     * @JMS\Type("string")
     */
    public $name;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("sessionTypes")
     */
    public $sessionTypes;

    public function __construct(
        $id,
        $name
    ) {
        $this->id = $id;
        $this->name = $name;

        $this->sessionTypes = [];
    }
}
