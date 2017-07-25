<?php

namespace Ilios\CoreBundle\Classes;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class CalendarEvent
 *
 *@IS\DTO
 */
abstract class CalendarEvent
{
    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     **/
    public $name;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     **/
    public $courseTitle;

    /**
     * @var \DateTime
     * @IS\Expose
     * @IS\Type("dateTime")
     **/
    public $startDate;

    /**
     * @var \DateTime
     * @IS\Expose
     * @IS\Type("dateTime")
     **/
    public $endDate;

    /**
     * @var Integer
     * @IS\Expose
     * @IS\Type("integer")
     **/
    public $offering;

    /**
     * @var Integer
     * @IS\Expose
     * @IS\Type("integer")
     **/
    public $ilmSession;

    /**
     * @var string
     *
     * @deprecated use color instead
     *
     * @IS\Expose
     * @IS\Type("string")
     **/
    public $eventClass;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     **/
    public $color;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     **/
    public $location;

    /**
     * @var \DateTime
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public $lastModified;

    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $isPublished;

    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $isScheduled;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    public $instructors = array();

    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $attireRequired;

    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $equipmentRequired;

    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $supplemental;

    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $attendanceRequired;

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
            $this->color = null;
            $this->location = null;
            $this->attireRequired = null;
            $this->equipmentRequired = null;
            $this->supplemental = null;
            $this->attendanceRequired = null;

            $this->instructors = [];
        }
    }
}
