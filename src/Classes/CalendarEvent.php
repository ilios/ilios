<?php

declare(strict_types=1);

namespace App\Classes;

use App\Annotation as IS;
use App\Entity\LearningMaterialInterface;
use App\Entity\LearningMaterialStatusInterface;
use DateTime;

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
     * @var DateTime
     * @IS\Expose
     * @IS\Type("dateTime")
     **/
    public $startDate;

    /**
     * @var DateTime
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
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     **/
    public $url;

    /**
     * @var DateTime
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
     * @IS\Type("array<string>")
     */
    public $instructors = [];

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<dto>")
     */
    public $learningMaterials = [];

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
     * @IS\Type("integer")
     */
    public $session;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $course;

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
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $sessionTypeId;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $sessionTypeTitle;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array")
     */
    public $sessionObjectives = [];

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array")
     */
    public $courseObjectives = [];

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array")
     */
    public $competencies = [];

    /**
     * @var []
     * @IS\Expose
     * @IS\Type("array<dto>")
     */
    public $postrequisites = [];

    /**
     * @var []
     * @IS\Expose
     * @IS\Type("array")
     */
    public $cohorts = [];

    /**
     * @var []
     * @IS\Expose
     * @IS\Type("array<dto>")
     */
    public $prerequisites = [];

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     **/
    public $school;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $courseLevel;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array")
     */
    public $sessionTerms = [];

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array")
     */
    public $courseTerms = [];

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
            $this->url = null;
            $this->attireRequired = null;
            $this->equipmentRequired = null;
            $this->supplemental = null;
            $this->attendanceRequired = null;
            $this->courseExternalId = null;
            $this->sessionDescription = null;
            $this->sessionTitle = null;
            $this->sessionTypeTitle = null;
            $this->sessionTypeId = null;
            $this->courseLevel = null;

            $this->instructors = [];
            $this->learningMaterials = [];
            $this->sessionObjectives = [];
            $this->courseObjectives = [];
            $this->competencies = [];
            $this->cohorts = [];
            $this->sessionTerms = [];
            $this->courseTerms = [];
        }
    }

    /**
     * Removes any materials that are in draft mode.
     */
    protected function removeMaterialsInDraft()
    {
        $this->learningMaterials = array_values(
            array_filter(
                $this->learningMaterials,
                fn(UserMaterial $lm) => $lm->status !== LearningMaterialStatusInterface::IN_DRAFT
            )
        );
    }
}
