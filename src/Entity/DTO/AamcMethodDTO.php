<?php

namespace App\Entity\DTO;

use App\Annotation as IS;

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

    /**
     * @var boolean
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $active;


    public function __construct(
        $id,
        $description,
        $active

    ) {
        $this->id = $id;
        $this->description = $description;
        $this->active = $active;

        $this->sessionTypes = [];
    }
}
