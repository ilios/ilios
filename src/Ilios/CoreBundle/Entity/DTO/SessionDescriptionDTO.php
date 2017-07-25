<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

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
     * @var integer
     * @IS\Expose
     * @IS\Type("string")
     */
    public $session;

    /**
     * @var integer
     * Not exposed, needed for the voter
     *
     * @IS\Type("string")
     */
    public $course;

    /**
     * @var integer
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
