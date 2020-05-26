<?php

declare(strict_types=1);

namespace App\Classes;

use App\Annotation as IS;

/**
 * Class AcademicYear
 *
 * @IS\DTO("academicYears")
 */

class AcademicYear
{
    /**
     * @var string
     *
     * @IS\Id
     * @IS\Expose
     * @IS\Type("string")
     */
    public $id;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $title;

    /**
     * Set the year and set both the id and title to it
     * @param string $year
     */
    public function __construct($year)
    {
        $this->id = $year;
        $this->title = $year;
    }
}
