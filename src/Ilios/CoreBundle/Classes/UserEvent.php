<?php

namespace Ilios\CoreBundle\Classes;

use JMS\Serializer\Annotation as JMS;

/**
 * Class UserEvent
 * @package Ilios\CoreBundle\Classes
 *
 * @JMS\ExclusionPolicy("all")
 */
class UserEvent
{

    /**
     * @var int
     * @JMS\Expose
     * @JMS\Type("integer")
     **/
    public $user;

    /**
     * @var int
     * @JMS\Expose
     * @JMS\Type("string")
     **/
    public $name;

    /**
     * @var DateTime
     * @JMS\Expose
     * @JMS\Type("DateTime")
     * @JMS\SerializedName("startDate")
     **/
    public $startDate;

    /**
     * @var DateTime
     * @JMS\Expose
     * @JMS\Type("DateTime")
     * @JMS\SerializedName("endDate")
     **/
    public $endDate;

    /**
     * @var Integer
     * @JMS\Expose
     * @JMS\Type("integer")
     **/
    public $offering;

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("eventClass")
     **/
    public $eventClass;

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     **/
    public $location;

    /**
     * @var \DateTime
     * @JMS\Expose
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("lastModified")
     */
    public $lastModified;

    /**
     * @var bool
     * @JMS\Expose
     * @JMS\Type("boolean")
     * @JMS\SerializedName("isPublished")
     */
    public $isPublished;

    /**
     * @var bool
     * @JMS\Expose
     * @JMS\Type("boolean")
     * @JMS\SerializedName("isScheduled")
     */
    public $isScheduled;
}
