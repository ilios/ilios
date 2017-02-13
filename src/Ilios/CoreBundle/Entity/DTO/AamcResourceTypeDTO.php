<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class AamcResourceTypeDTO
 * Data transfer object for a aamcResourceType
 * @package Ilios\CoreBundle\Entity\DTO
 */
class AamcResourceTypeDTO
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
    public $title;

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
