<?php

declare(strict_types=1);

namespace App\Classes;

use App\Attribute as IA;
use DateTime;

/**
 * Class UserMaterial
 */
#[IA\DTO('userMaterial')]
class UserMaterial
{
    /**
     * A list of 'do not scrub' properties.
     */
    protected static array $doNotScrubProps = [
        'id',
        'courseLearningMaterial',
        'sessionLearningMaterial',
        'position',
        'title',
        'course',
        'courseTitle',
        'session',
        'sessionTitle',
        'startDate',
        'endDate',
        'isBlanked',
        'firstOfferingDate',
    ];
    /**
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public int $id;
    /**
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public ?int $courseLearningMaterial = null;
    /**
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public ?int $sessionLearningMaterial = null;
    /**
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public ?int $session = null;
    /**
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public ?int $course = null;
    /**
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $publicNotes = null;
    /**
     */
    #[IA\Expose]
    #[IA\Type('boolean')]
    public ?bool $required = null;
    /**
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public string $title;
    /**
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $description = null;
    /**
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $originalAuthor = null;
    /**
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $absoluteFileUri = null;
    /**
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $citation = null;
    /**
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $link = null;
    /**
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $filename = null;
    /**
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public ?int $filesize = null;
    /**
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public ?int $position = null;
    /**
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $mimetype = null;
    /**
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $sessionTitle = null;
    /**
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $courseTitle = null;
    /**
     */
    #[IA\Expose]
    #[IA\Type('dateTime')]
    public ?DateTime $firstOfferingDate = null;
    /**
     */
    #[IA\Expose]
    #[IA\Type('array<string>')]
    public array $instructors = [];
    /**
     */
    #[IA\Expose]
    #[IA\Type('dateTime')]
    public ?DateTime $startDate = null;
    /**
     */
    #[IA\Expose]
    #[IA\Type('dateTime')]
    public ?DateTime $endDate = null;
    /**
     */
    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $isBlanked = false;
    public ?int $status = null;
    /**
     * Blanks out properties of timed learning materials that are outside their given
     * timed-release window (relative to the given date-time).
     * And sets the isBlanked flag to TRUE, as applicable.
     */
    public function clearTimedMaterial(\DateTime $dateTime)
    {
        $startDate = $this->startDate;
        $endDate = $this->endDate;
        $blankThis = false;
        if (isset($startDate) && isset($endDate)) {
            $blankThis = ($startDate > $dateTime || $dateTime > $endDate);
        } elseif (isset($startDate)) {
            $blankThis = ($startDate > $dateTime);
        } elseif (isset($endDate)) {
            $blankThis = ($dateTime > $endDate);
        }

        if ($blankThis) {
            $this->clearMaterial();
        }
    }
    /**
     * Blanks out most data points of this learning material.
     */
    public function clearMaterial()
    {
        $this->isBlanked = true;
        $props = array_keys(get_object_vars($this));
        foreach ($props as $prop) {
            if (! in_array($prop, self::$doNotScrubProps) && $prop !== 'instructors') {
                $this->$prop = null;
            }
        }
        $this->instructors = [];
    }
}
