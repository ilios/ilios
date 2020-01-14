<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class AamcMethodDTO
 * Data transfer object for a aamcMethod
 *
 * @IS\DTO
 */
class DepartmentDTO
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
    */
    public $title;

    /**
     * @var int
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
