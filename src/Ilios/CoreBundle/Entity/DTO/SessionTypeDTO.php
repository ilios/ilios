<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class SessionTypeDTO
 * Data transfer object for a session type
 * @package Ilios\CoreBundle\Entity\DTO
 *
 * @IS\DTO
 */
class SessionTypeDTO
{
    /**
     * @var integer
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
    */
    public $title;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
    */
    public $calendarColor;

    /**
     * @var integer
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    public $assessmentOption;

    /**
     * @var integer
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    public $school;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $aamcMethods;

    /**
     * SessionTypeDTO constructor.
     * @param $id
     * @param $title
     * @param $calendarColor
     */
    public function __construct($id, $title, $calendarColor)
    {
        $this->id = $id;
        $this->title = $title;
        $this->calendarColor = $calendarColor;

        $this->aamcMethods = [];
    }
}
