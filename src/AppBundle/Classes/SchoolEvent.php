<?php

namespace AppBundle\Classes;

use AppBundle\Annotation as IS;

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
