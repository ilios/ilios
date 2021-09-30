<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use DateTime;

/**
 * Class OfferingDTO
 */
#[IA\DTO('offerings')]
#[IA\ExposeGraphQL]
class OfferingDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $room;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $site;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $url;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $startDate;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $endDate;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $updatedAt;

    #[IA\Expose]
    #[IA\Related('sessions')]
    #[IA\Type('integer')]
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
    public array $learners = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type('array<string>')]
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
