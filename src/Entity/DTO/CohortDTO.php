<?php

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class CohortDTO
 * Data transfer object for a cohort
 *
 * @IS\DTO
 */
class CohortDTO
{
    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     *
     */
    public $title;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $programYear;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $courses;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $learnerGroups;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $users;


    /**
     * For Voter use, not public
     * @var int
     */
    public $program;

    /**
     * For Voter use, not public
     * @var int
     */
    public $school;

    public function __construct(
        $id,
        $title
    ) {
        $this->id = $id;
        $this->title = $title;

        $this->courses = [];
        $this->learnerGroups = [];
        $this->users = [];
    }
}
