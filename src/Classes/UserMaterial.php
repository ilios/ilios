<?php

declare(strict_types=1);

namespace App\Classes;

use App\Annotation as IS;
use DateTime;

/**
 * Class UserMaterial
 *
 * @IS\DTO
 */
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
     * @IS\Expose
     * @IS\Type("string")
     */
    public int $id;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?int $courseLearningMaterial = null;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?int $sessionLearningMaterial = null;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?int $session = null;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?int $course = null;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $publicNotes = null;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public ?bool $required = null;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $title;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $description = null;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $originalAuthor = null;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $absoluteFileUri = null;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $citation = null;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $link = null;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $filename = null;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?int $filesize = null;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?int $position = null;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $mimetype = null;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $sessionTitle = null;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $courseTitle = null;

    /**
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public ?DateTime $firstOfferingDate = null;

    /**
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public array $instructors = [];

    /**
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public ?DateTime $startDate = null;

    /**
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public ?DateTime $endDate = null;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $isBlanked = false;

    public ?int $status = null;

    /**
     * Blanks out properties of timed learning materials that are outside their given
     * timed-release window (relative to the given date-time).
     * And sets the isBlanked flag to TRUE, as applicable.
     *
     * @param \DateTime $dateTime
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
