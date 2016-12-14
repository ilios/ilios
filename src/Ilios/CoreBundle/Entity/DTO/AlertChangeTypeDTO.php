<?php

namespace Ilios\CoreBundle\Entity\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * Class AlertChangeTypeDTO
 * Data transfer object for a alertChangeType
 * @package Ilios\CoreBundle\Entity\DTO
 */
class AlertChangeTypeDTO
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
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $alerts;

    public function __construct(
        $id,
        $title
    ) {
        $this->id = $id;
        $this->title = $title;

        $this->alerts = [];
    }
}
