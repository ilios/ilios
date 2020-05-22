<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class SessionTypeDTO
 * Data transfer object for a session type
 *
 * @IS\DTO("sessionTypes")
 */
class SessionTypeDTO
{
    /**
     * @var int
     * @IS\Id
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
     * @var bool
     *
     * @IS\Expose
     * @IS\Type("boolean")
    */
    public $active;

    /**
     * @var bool
     *
     * @IS\Expose
     * @IS\Type("boolean")
    */
    public $assessment;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Related("assessmentOptions")
     * @IS\Type("entity")
     */
    public $assessmentOption;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Related("schools")
     * @IS\Type("entity")
     */
    public $school;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $aamcMethods;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $sessions;

    /**
     * SessionTypeDTO constructor.
     * @param $id
     * @param $title
     * @param $calendarColor
     * @param $assessment
     * @param $active
     */
    public function __construct($id, $title, $calendarColor, $assessment, $active)
    {
        $this->id = $id;
        $this->title = $title;
        $this->calendarColor = $calendarColor;
        $this->assessment = $assessment;
        $this->active = $active;

        $this->aamcMethods = [];
        $this->sessions = [];
    }
}
