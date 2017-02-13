<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class AamcMethodDTO
 * Data transfer object for a aamcMethod
 * @package Ilios\CoreBundle\Entity\DTO
 */
class AamcMethodDTO
{
    /**
     * @var int
     * @IS\Type("string")
     */
    public $id;

    /**
     * @var string
     * @IS\Type("string")
     *
     */
    public $description;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $sessionTypes;

    public function __construct(
        $id,
        $description
    ) {
        $this->id = $id;
        $this->description = $description;

        $this->sessionTypes = [];
    }
}
