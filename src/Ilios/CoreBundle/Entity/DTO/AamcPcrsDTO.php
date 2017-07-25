<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class AamcPcrsDTO
 * Data transfer object for a aamcPcrs
 *
 * @IS\DTO
 */
class AamcPcrsDTO
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
