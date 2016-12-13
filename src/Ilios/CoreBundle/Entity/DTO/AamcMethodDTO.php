<?php

namespace Ilios\CoreBundle\Entity\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * Class AamcMethodDTO
 * Data transfer object for a aamcMethod
 * @package Ilios\CoreBundle\Entity\DTO
 */
class AamcMethodDTO
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
     * @JMS\SerializedName("sessionTypes")
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
