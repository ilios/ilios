<?php

namespace Ilios\CoreBundle\Classes;

use JMS\Serializer\Annotation as JMS;

/**
 * Class UserEvent
 * @package Ilios\CoreBundle\Classes
 *
 * @JMS\ExclusionPolicy("all")
 */
class UserEvent extends CalendarEvent
{
    /**
     * @var int
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    public $user;
}
