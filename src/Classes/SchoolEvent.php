<?php

namespace App\Classes;

use App\Annotation as IS;

/**
 * Class SchoolEvent
 *
 * @IS\DTO
 */
class SchoolEvent extends CalendarEvent
{
    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     **/
    public $school;
}
