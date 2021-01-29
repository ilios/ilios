<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;
use DateTime;

/**
 * Class OfferingDTO
 * Data transfer object for a offering
 *
 * @IS\DTO("offerings")
 */
class OfferingDTO
{
    /**
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $id;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $room;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $site;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $url;

    /**
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public DateTime $startDate;

    /**
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public DateTime $endDate;

    /**
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public DateTime $updatedAt;

    /**
     * @IS\Expose
     * @IS\Related("sessions")
     * @IS\Type("integer")
     */
    public int $session;

    /**
     * For Voter use, not public
     */
    public int $course;

    /**
     * For Voter use, not public
     */
    public int $school;

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
    public array $learners = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("array<string>")
     */
    public array $instructors = [];

    public function __construct(
        int $id,
        ?string $room,
        ?string $site,
        ?string $url,
        DateTime $startDate,
        DateTime $endDate,
        DateTime $updatedAt
    ) {
        $this->id = $id;
        $this->room = $room;
        $this->site = $site;
        $this->url = $url;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->updatedAt = $updatedAt;
    }
}
