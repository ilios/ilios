<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class SessionDescriptionDTO
 *
 * @IS\DTO
 */
class SessionDescriptionDTO
{
    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $id;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $description;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $session;

    /**
     * @var int
     * Not exposed, needed for the voter
     *
     * @IS\Type("string")
     */
    public $course;

    /**
     * @var int
     * Not exposed, needed for the voter
     *
     * @IS\Type("string")
     */
    public $school;

    /**
     * SessionDescriptionDTO constructor.
     * @param $id
     * @param $description
     */
    public function __construct($id, $description)
    {
        $this->id = $id;
        $this->description = $description;
    }
}
