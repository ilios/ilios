<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class SessionObjectiveDTO
 *
 * @IS\DTO("sessionObjectives")
 */
class SessionObjectiveDTO
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
     * @IS\Type("integer")
     */
    public $session;

    /**
     * @var int
     *
     * @IS\Expose
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
     * Needed for Voting, not exposed in the API
     * @var int
     *
     * @IS\Type("integer")
     */
    public $course;

    /**
     * Needed for Voting, not exposed in the API
     * @var int
     *
     * @IS\Type("integer")
     */
    public $school;

    /**
     * Needed for Voting, not exposed in the API
     * @var bool
     *
     * @IS\Type("boolean")
     */
    public $courseIsLocked;

    /**
     * Needed for Voting, not exposed in the API
     * @var bool
     *
     * @IS\Type("boolean")
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
