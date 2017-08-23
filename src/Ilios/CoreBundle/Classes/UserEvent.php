<?php

namespace Ilios\CoreBundle\Classes;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class UserEvent
 *
 * @IS\DTO
 */
class UserEvent extends CalendarEvent
{
    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $user;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $courseExternalId;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $sessionTitle;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $sessionDescription;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $sessionTypeTitle;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    public $learningMaterials = array();

    /**
     * @var int
     */
    public $sessionId;

    /**
     * @var int
     */
    public $courseId;

    /**
     * @inheritdoc
     */
    public function clearDataForScheduledEvent()
    {
        parent::clearDataForScheduledEvent();
        if ($this->isScheduled) {
            $this->courseExternalId = null;
            $this->sessionDescription = null;
            $this->sessionTitle = null;
            $this->sessionTypeTitle = null;
            $this->learningMaterials = [];
        }
    }

    /**
     * @param \DateTime $dateTime
     */
    public function clearTimedMaterials(\DateTime $dateTime) {
        $doNotScrubProps = array(
            'id',
            'title',
            'course',
            'courseTitle',
            'session',
            'sessionTitle',
            'startDate',
            'endDate',
            'isBlanked'
        );
        foreach ($this->learningMaterials as $lm) {
            $startDate = $lm->startDate;
            $endDate = $lm->endDate;
            $blankThis = false;
            if (isset($startDate) && isset($endDate)) {
                $blankThis = ($startDate > $dateTime || $dateTime > $endDate);
            } elseif (isset($startDate)) {
                $blankThis = ($startDate > $dateTime);
            } elseif (isset($enDate)) {
                $blankThis = ($dateTime > $endDate);
            }

            if ($blankThis) {
                $lm->isBlanked = true;
                $props = array_keys(get_object_vars($lm));
                foreach($props as $prop) {
                    if (! in_array($prop, $doNotScrubProps)) {
                        $lm->$prop = null;
                    }
                }
            }
        }
    }
}
