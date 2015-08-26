<?php

namespace Ilios\CoreBundle\Classes;

use JMS\Serializer\Annotation as JMS;

/**
 * Class AcademicYear
 * @package Ilios\CoreBundle\Classes
 *
 * @JMS\ExclusionPolicy("all")
 */

class AcademicYear
{
    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $id;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $title;

    /**
     * Set the year and set botht he id and title to it
     * @param string $year
     */
    public function __construct($year)
    {
        $this->id = $year;
        $this->title = $year;
    }

    /**
     * get the id
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * get the title
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
