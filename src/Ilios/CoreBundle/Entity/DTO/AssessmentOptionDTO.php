<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class AssessmentOptionDTO
 * Data transfer object for an assessmentOption
 *
 * @IS\DTO
 */
class AssessmentOptionDTO
{
    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $name;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
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
