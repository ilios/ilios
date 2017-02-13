<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class AssessmentOptionDTO
 * Data transfer object for an assessmentOption
 * @package Ilios\CoreBundle\Entity\DTO
 */
class AssessmentOptionDTO
{
    /**
     * @var int
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var int
     * @IS\Type("string")
     */
    public $name;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
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
