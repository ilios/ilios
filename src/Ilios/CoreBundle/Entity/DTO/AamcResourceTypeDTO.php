<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class AamcResourceTypeDTO
 * Data transfer object for a aamcResourceType
 *
 * @IS\DTO
 */
class AamcResourceTypeDTO
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
    public $title;

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
