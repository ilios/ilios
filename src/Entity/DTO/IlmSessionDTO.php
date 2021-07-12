<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use DateTime;

/**
 * Class IlmSessionDTO
 */
#[IA\DTO('ilmSessions')]
class IlmSessionDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Related('sessions')]
    #[IA\Type('integer')]
    public int $session;

    #[IA\Expose]
    #[IA\Type('float')]
    public float $hours;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $dueDate;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $learnerGroups = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $instructorGroups = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type('array<string>')]
    public array $instructors = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type('array<string>')]
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
