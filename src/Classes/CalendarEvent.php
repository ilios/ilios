<?php

declare(strict_types=1);

namespace App\Classes;

use App\Attributes as IA;
use App\Entity\LearningMaterialStatusInterface;
use DateTime;

/**
 * Class CalendarEvent
 */
class CalendarEvent
{
    #[IA\Expose]
    #[IA\Type('string')]
    public string $name = '';

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $courseTitle = null;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $startDate;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $endDate;

    #[IA\Expose]
    #[IA\Type('integer')]
    public ?int $offering = null;

    #[IA\Expose]
    #[IA\Type('integer')]
    public ?int $ilmSession = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $color = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $location = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $url = null;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $lastModified;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $isPublished = false;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $isScheduled = false;

    #[IA\Expose]
    #[IA\Type(IA\Type::STRINGS)]
    public array $instructors = [];

    #[IA\Expose]
    #[IA\Type(IA\Type::DTOS)]
    public array $learningMaterials = [];

    #[IA\Expose]
    #[IA\Type('boolean')]
    public ?bool $attireRequired = null;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public ?bool $equipmentRequired = null;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public ?bool $supplemental = null;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public ?bool $attendanceRequired = null;

    #[IA\Expose]
    #[IA\Type('integer')]
    public ?int $session = null;

    #[IA\Expose]
    #[IA\Type('integer')]
    public ?int $course = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $courseExternalId = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $sessionTitle = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $sessionDescription = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $instructionalNotes = null;

    #[IA\Expose]
    #[IA\Type('integer')]
    public ?int $sessionTypeId = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $sessionTypeTitle = null;

    #[IA\Expose]
    #[IA\Type('array')]
    public array $sessionObjectives = [];

    #[IA\Expose]
    #[IA\Type('array')]
    public array $courseObjectives = [];

    #[IA\Expose]
    #[IA\Type('array')]
    public array $competencies = [];

    #[IA\Expose]
    #[IA\Type(IA\Type::DTOS)]
    public array $postrequisites = [];

    #[IA\Expose]
    #[IA\Type('array')]
    public array $cohorts = [];

    #[IA\Expose]
    #[IA\Type(IA\Type::DTOS)]
    public array $prerequisites = [];

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $school;

    #[IA\Expose]
    #[IA\Type('integer')]
    public ?int $courseLevel = null;

    #[IA\Expose]
    #[IA\Type('array')]
    public array $sessionTerms = [];

    #[IA\Expose]
    #[IA\Type('array')]
    public array $courseTerms = [];

    #[IA\Expose]
    #[IA\Type(IA\Type::STRINGS)]
    public array $userContexts = [];

    /**
     * Clean out all the data for draft or scheduled events
     *
     * This information is not available to un-privileged users
     */
    protected function clearDataForDraftOrScheduledEvent(): void
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
    protected function removeMaterialsInDraft(): void
    {
        $this->learningMaterials = array_values(
            array_filter(
                $this->learningMaterials,
                fn(UserMaterial $lm) => $lm->status !== LearningMaterialStatusInterface::IN_DRAFT
            )
        );
    }
}
