<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class ApplicationConfigDTO
 * Data transfer object for an applicationConfig
 *
 * @IS\DTO
 */
class ApplicationConfigDTO
{
    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $name;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $value;

    public function __construct($id, $name, $value)
    {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
    }
}
