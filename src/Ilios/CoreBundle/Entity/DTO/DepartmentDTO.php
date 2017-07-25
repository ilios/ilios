<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class AamcMethodDTO
 * Data transfer object for a aamcMethod
 *
 * @IS\DTO
 */
class DepartmentDTO
{
    /**
     * @var integer
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
    */
    public $title;

    /**
     * @var integer
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $school;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $stewards;

    /**
     * Constructor
     */
    public function __construct($id, $title)
    {
        $this->id = $id;
        $this->title = $title;

        $this->stewards = [];
    }
}
