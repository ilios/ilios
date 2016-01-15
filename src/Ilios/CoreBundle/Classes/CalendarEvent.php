<?php

namespace Ilios\CoreBundle\Classes;

use JMS\Serializer\Annotation as JMS;

/**
 * Class CalendarEvent
 * @package Ilios\CoreBundle\Classes
 *
 *@JMS\ExclusionPolicy("all")
 */
abstract class CalendarEvent
{
    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     **/
    public $name;

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("courseTitle")
     **/
    public $courseTitle;

    /**
     * @var \DateTime
     * @JMS\Expose
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("startDate")
     **/
    public $startDate;

    /**
     * @var \DateTime
     * @JMS\Expose
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("endDate")
     **/
    public $endDate;

    /**
     * @var Integer
     * @JMS\Expose
     * @JMS\Type("integer")
     **/
    public $offering;

    /**
     * @var Integer
     * @JMS\Expose
     * @JMS\Type("integer")
     * @JMS\SerializedName("ilmSession")
     **/
    public $ilmSession;

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("eventClass")
     **/
    public $eventClass;

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     **/
    public $location;

    /**
     * @var \DateTime
     * @JMS\Expose
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("lastModified")
     */
    public $lastModified;

    /**
     * @var bool
     * @JMS\Expose
     * @JMS\Type("boolean")
     * @JMS\SerializedName("isPublished")
     */
    public $isPublished;

    /**
     * @var bool
     * @JMS\Expose
     * @JMS\Type("boolean")
     * @JMS\SerializedName("isScheduled")
     */
    public $isScheduled;

    /**
     * @var array
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("instructors")
     */
    public $instructors = array();

    /**
     * Clean out all the data for scheduled events
     *
     * This information is not available to un-privileged users
     */
    public function clearDataForScheduledEvent()
    {
        if ($this->isScheduled) {
            $this->name = 'Scheduled';
            $this->courseTitle = null;
            $this->offering = null;
            $this->ilmSession = null;
            $this->eventClass = null;
            $this->location = null;
            $this->instructors = [];
        }
    }
}
