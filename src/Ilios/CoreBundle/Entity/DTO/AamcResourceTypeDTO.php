<?php

namespace Ilios\CoreBundle\Entity\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * Class AamcResourceTypeDTO
 * Data transfer object for a aamcResourceType
 * @package Ilios\CoreBundle\Entity\DTO
 */
class AamcResourceTypeDTO
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
    public $title;

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
    public $terms;

    public function __construct(
        $id,
        $title,
        $description
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;

        $this->terms = [];
    }
}
