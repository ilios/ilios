<?php

namespace Ilios\CoreBundle\Entity\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * Class AamcPcrsDTO
 * Data transfer object for a aamcPcrs
 * @package Ilios\CoreBundle\Entity\DTO
 */
class AamcPcrsDTO
{
    /**
     * @var int
     * @JMS\Type("string")
     */
    public $id;

    /**
     * @var string
     * @JMS\Type("string")
     *
     */
    public $description;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
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
