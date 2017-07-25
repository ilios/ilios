<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class AlertChangeTypeDTO
 * Data transfer object for a alertChangeType
 *
 * @IS\DTO
 */
class AlertChangeTypeDTO
{
    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     *
     */
    public $title;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
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
