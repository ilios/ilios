<?php

namespace Ilios\CoreBundle\Classes;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class AcademicYear
 *
 * @IS\DTO
 */

class AcademicYear
{
    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $id;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $title;

    /**
     * Set the year and set both the id and title to it
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
