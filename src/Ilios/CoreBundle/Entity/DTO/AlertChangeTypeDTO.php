<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class AlertChangeTypeDTO
 * Data transfer object for a alertChangeType
 * @package Ilios\CoreBundle\Entity\DTO
 */
class AlertChangeTypeDTO
{
    /**
     * @var int
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @IS\Type("string")
     *
     */
    public $title;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
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
