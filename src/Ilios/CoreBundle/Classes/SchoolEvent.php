<?php

namespace Ilios\CoreBundle\Classes;

use JMS\Serializer\Annotation as JMS;

/**
 * Class SchoolEvent
 * @package Ilios\CoreBundle\Classes
 *
 * @JMS\ExclusionPolicy("all")
 */
class SchoolEvent extends CalendarEvent
{
    /**
     * @var int
     * @JMS\Expose
     * @JMS\Type("integer")
     **/
    public $school;
}
