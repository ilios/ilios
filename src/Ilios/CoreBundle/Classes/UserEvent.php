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
     * @inheritdoc
     */
    public function clearDataForDraftOrScheduledEvent()
    {
        parent::clearDataForDraftOrScheduledEvent();
        if (!$this->isPublished || $this->isScheduled) {
            $this->courseExternalId = null;
            $this->sessionDescription = null;
            $this->sessionTitle = null;
            $this->sessionTypeTitle = null;
        }
    }
}
