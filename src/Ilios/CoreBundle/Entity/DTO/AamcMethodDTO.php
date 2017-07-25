<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class AamcMethodDTO
 * Data transfer object for a aamcMethod
 *
 * @IS\DTO
 */
class AamcMethodDTO
{
    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $id;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     *
     */
    public $description;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
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
