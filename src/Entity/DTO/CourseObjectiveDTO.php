<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class CourseObjectiveDTO
 *
 * @IS\DTO("courseObjectives")
 */
class CourseObjectiveDTO
{
    /**
     * @var int
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Related("objectives")
     * @IS\Type("integer")
     */
    public $objective;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     *
     */
    public $position;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $terms;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Related("courses")
     * @IS\Type("integer")
     */
    public $course;

    /**
     * Needed for Voting, not exposed in the API
     * @var int
     */
    public $school;

    /**
     * Needed for Voting, not exposed in the API
     * @var bool
     */
    public $courseIsLocked;

    /**
     * Needed for Voting, not exposed in the API
     * @var bool
     */
    public $courseIsArchived;


    /**
     * Constructor
     * @param int $id
     * @param int $position
     */
    public function __construct($id, $position)
    {
        $this->id = $id;
        $this->position = $position;
        $this->terms = [];
    }
}
