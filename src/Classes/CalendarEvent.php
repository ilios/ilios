<?php

declare(strict_types=1);

namespace App\Classes;

use App\Attribute as IA;
use App\Entity\LearningMaterialInterface;
use App\Entity\LearningMaterialStatusInterface;
use DateTime;

/**
 * Class CalendarEvent
 */
#[IA\DTO]
class CalendarEvent
{
    /**
     * @var string
     **/
    #[IA\Expose]
    #[IA\Type('string')]
    public $name;
    /**
     * @var string
     **/
    #[IA\Expose]
    #[IA\Type('string')]
    public $courseTitle;
    /**
     * @var DateTime
     **/
    #[IA\Expose]
    #[IA\Type('dateTime')]
    public $startDate;
    /**
     * @var DateTime
     **/
    #[IA\Expose]
    #[IA\Type('dateTime')]
    public $endDate;
    /**
     * @var Integer
     **/
    #[IA\Expose]
    #[IA\Type('integer')]
    public $offering;
    /**
     * @var Integer
     **/
    #[IA\Expose]
    #[IA\Type('integer')]
    public $ilmSession;
    /**
     * @var string
     **/
    #[IA\Expose]
    #[IA\Type('string')]
    public $color;
    /**
     * @var string
     **/
    #[IA\Expose]
    #[IA\Type('string')]
    public $location;
    /**
     * @var string
     **/
    #[IA\Expose]
    #[IA\Type('string')]
    public $url;
    /**
     * @var DateTime
     */
    #[IA\Expose]
    #[IA\Type('dateTime')]
    public $lastModified;
    /**
     * @var bool
     */
    #[IA\Expose]
    #[IA\Type('boolean')]
    public $isPublished;
    /**
     * @var bool
     */
    #[IA\Expose]
    #[IA\Type('boolean')]
    public $isScheduled;
    /**
     * @var array
     */
    #[IA\Expose]
    #[IA\Type('array<string>')]
    public $instructors = [];
    /**
     * @var array
     */
    #[IA\Expose]
    #[IA\Type('array<dto>')]
    public $learningMaterials = [];
    /**
     * @var bool
     */
    #[IA\Expose]
    #[IA\Type('boolean')]
    public $attireRequired;
    /**
     * @var bool
     */
    #[IA\Expose]
    #[IA\Type('boolean')]
    public $equipmentRequired;
    /**
     * @var bool
     */
    #[IA\Expose]
    #[IA\Type('boolean')]
    public $supplemental;
    /**
     * @var bool
     */
    #[IA\Expose]
    #[IA\Type('boolean')]
    public $attendanceRequired;
    /**
     * @var int
     */
    #[IA\Expose]
    #[IA\Type('integer')]
    public $session;
    /**
     * @var int
     */
    #[IA\Expose]
    #[IA\Type('integer')]
    public $course;
    /**
     * @var string
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public $courseExternalId;
    /**
     * @var string
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public $sessionTitle;
    /**
     * @var string
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public $sessionDescription;
    /**
     * @var string
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public $instructionalNotes;
    /**
     * @var int
     */
    #[IA\Expose]
    #[IA\Type('integer')]
    public $sessionTypeId;
    /**
     * @var string
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public $sessionTypeTitle;
    /**
     * @var array
     */
    #[IA\Expose]
    #[IA\Type('array')]
    public $sessionObjectives = [];
    /**
     * @var array
     */
    #[IA\Expose]
    #[IA\Type('array')]
    public $courseObjectives = [];
    /**
     * @var array
     */
    #[IA\Expose]
    #[IA\Type('array')]
    public $competencies = [];
    /**
     * @var []
     */
    #[IA\Expose]
    #[IA\Type('array<dto>')]
    public $postrequisites = [];
    /**
     * @var []
     */
    #[IA\Expose]
    #[IA\Type('array')]
    public $cohorts = [];
    /**
     * @var []
     */
    #[IA\Expose]
    #[IA\Type('array<dto>')]
    public $prerequisites = [];
    /**
     * @var int
     **/
    #[IA\Expose]
    #[IA\Type('integer')]
    public $school;
    /**
     * @var int
     */
    #[IA\Expose]
    #[IA\Type('integer')]
    public $courseLevel;
    /**
     * @var array
     */
    #[IA\Expose]
    #[IA\Type('array')]
    public $sessionTerms = [];
    /**
     * @var array
     */
    #[IA\Expose]
    #[IA\Type('array')]
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
