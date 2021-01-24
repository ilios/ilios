<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;
use DateTime;

/**
 * Class IlmSessionDTO
 * Data transfer object for a ilmSession
 *
 * @IS\DTO("ilmSessions")
 */
class IlmSessionDTO
{
    /**
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $id;

    /**
     * @IS\Expose
     * @IS\Related("sessions")
     * @IS\Type("string")
     */
    public int $session;

    /**
     * @IS\Expose
     * @IS\Type("float")
     */
    public float $hours;

    /**
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public DateTime $dueDate;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $learnerGroups = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $instructorGroups = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("array<string>")
     */
    public array $instructors = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("array<string>")
     */
    public array $learners = [];

    /**
     * Needed for voting not exposed in the API
     */
    public int $course;

    /**
     * Needed for voting not exposed in the API
     */
    public int $school;

    public function __construct(int $id, float $hours, DateTime $dueDate)
    {
        $this->id = $id;
        $this->hours = $hours;
        $this->dueDate = $dueDate;
    }
}
