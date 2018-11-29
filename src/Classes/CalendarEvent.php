<?php

namespace App\Classes;

use App\Annotation as IS;
use App\Entity\LearningMaterialInterface;
use App\Entity\LearningMaterialStatusInterface;

/**
 * Class CalendarEvent
 *
 *@IS\DTO
 */
class CalendarEvent
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
     * @var array
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    public $learningMaterials = array();

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
     * @var int
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $session;

    /**
     * @var int
     */
    public $courseId;

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
    public $instructionalNotes;

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
    public $sessionObjectives = array();

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    public $courseObjectives = array();

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    public $competencies = array();

    /**
     * @var []
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    public $postrequisiteSession;

    /**
     * @var []
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    public $prerequisiteSessions = array();

    /**
     * Clean out all the data for draft or scheduled events
     *
     * This information is not available to un-privileged users
     * @param \DateTime $dateTime
     */
    public function clearDataForUnprivilegedUsers(\DateTime $dateTime)
    {
        $this->instructionalNotes = null;
        $this->clearDataForDraftOrScheduledEvent();
        $this->removeMaterialsInDraft();
        $this->clearTimedMaterials($dateTime);
    }

    /**
     * Clean out all the data for draft or scheduled events
     *
     * This information is not available to un-privileged users
     */
    protected function clearDataForDraftOrScheduledEvent()
    {
        if (!$this->isPublished || $this->isScheduled) {
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
            $this->courseExternalId = null;
            $this->sessionDescription = null;
            $this->sessionTitle = null;
            $this->sessionTypeTitle = null;

            $this->instructors = [];
            $this->learningMaterials = [];
            $this->sessionObjectives = [];
            $this->courseObjectives = [];
            $this->competencies = [];
            $this->prerequisiteSessions = [];
        }
    }

    /**
     * Removes any materials that are in draft mode.
     */
    protected function removeMaterialsInDraft()
    {
        $this->learningMaterials = array_values(array_filter($this->learningMaterials, function (UserMaterial $lm) {
            return $lm->status !== LearningMaterialStatusInterface::IN_DRAFT;
        }));
    }


    /**
     * @param \DateTime $dateTime
     */
    protected function clearTimedMaterials(\DateTime $dateTime)
    {
        /** @var UserMaterial $lm */
        foreach ($this->learningMaterials as $lm) {
            $lm->clearTimedMaterial($dateTime);
        }
    }
}
