<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class AamcPcrsDTO
 * Data transfer object for a aamcPcrs
 * @package Ilios\CoreBundle\Entity\DTO
 */
class AamcPcrsDTO
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
    public $competencies;

    public function __construct(
        $id,
        $description
    ) {
        $this->id = $id;
        $this->description = $description;

        $this->competencies = [];
    }
}
