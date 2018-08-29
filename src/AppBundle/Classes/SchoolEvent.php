<?php

namespace AppBundle\Classes;

use Ilios\ApiBundle\Annotation as IS;

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
