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
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $mimetype;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $sessionTitle;

    /**
     * @var int
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

}
