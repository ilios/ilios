<?php

namespace Ilios\CoreBundle\Classes;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class UserMaterial
 *
 * @IS\DTO
 */
class UserMaterial
{
    /**
     * A list of 'do not scrub' properties.
     * @var array
     */
    protected static $doNotScrubProps = array(
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
    );

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $id;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $courseLearningMaterial;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $sessionLearningMaterial;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $session;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $course;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $publicNotes;

    /**
     * @var boolean
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $required;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $title;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $description;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $originalAuthor;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $absoluteFileUri;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $citation;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $link;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $filename;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $filesize;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $position;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $mimetype;

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
    public $courseTitle;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public $firstOfferingDate;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    public $instructors = [];

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public $startDate;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public $endDate;

    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $isBlanked;

    /**
     * @var int
     */
    public $status;

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
}
