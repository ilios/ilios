<?php

namespace Ilios\CoreBundle\Classes;

use JMS\Serializer\Annotation as JMS;

/**
 * Class UserMaterial
 * @package Ilios\CoreBundle\Classes
 *
 * @JMS\ExclusionPolicy("all")
 */
class UserMaterial
{
    /**
     * @var int
     * @JMS\Expose
     * @JMS\Type("string")
     */
    public $id;

    /**
     * @var int
     * @JMS\Expose
     * @JMS\Type("string")
     */
    public $session;

    /**
     * @var int
     * @JMS\Expose
     * @JMS\Type("string")
     */
    public $course;

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("publicNotes")
     */
    public $publicNotes;

    /**
     * @var boolean
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    public $required;

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     */
    public $title;

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     */
    public $description;

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("originalAuthor")
     */
    public $originalAuthor;

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("absoluteFileUri")
     */
    public $absoluteFileUri;

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     */
    public $citation;

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     */
    public $link;

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     */
    public $filename;

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     */
    public $mimetype;

    /**
     * @var int
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("sessionTitle")
     */
    public $sessionTitle;

    /**
     * @var int
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("courseTitle")
     */
    public $courseTitle;

}
