<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class IlmSessionDTO
 * Data transfer object for a ilmSession
 *
 * @IS\DTO("ilmSessions")
 */
class IlmSessionDTO
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
     * @IS\Related("sessions")
     * @IS\Type("string")
     */
    public $session;

    /**
     * @var float
     *
     * @IS\Expose
     * @IS\Type("float")
     */
    public $hours;

    /**
     * @var \DateTime
     *
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public $dueDate;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $learnerGroups;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $instructorGroups;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("array<string>")
     */
    public $instructors;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("array<string>")
     */
    public $learners;

    /**
     * Needed for voting not exposed in the API
     *
     * @var int
     */
    public $course;

    /**
     * Needed for voting not exposed in the API
     *
     * @var int
     */
    public $school;

    /**
     * Constructor
     */
    public function __construct($id, $hours, $dueDate)
    {
        $this->id = $id;
        $this->hours = $hours;
        $this->dueDate = $dueDate;
        
        $this->learnerGroups = [];
        $this->instructors = [];
        $this->instructorGroups = [];
        $this->learners = [];
    }
}
