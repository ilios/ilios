<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use DateTime;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Ignore;

#[IA\DTO('offerings')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "Offering",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "integer"
        ),
        new OA\Property(
            "room",
            description: "Room",
            type: "string"
        ),
        new OA\Property(
            "site",
            description: "Site",
            type: "string"
        ),
        new OA\Property(
            "url",
            description: "Virtual learning link",
            type: "string"
        ),
        new OA\Property(
            "startDate",
            description: "Start date",
            type: "string",
            format: "date-time"
        ),
        new OA\Property(
            "endDate",
            description: "End date",
            type: "string",
            format: "date-time"
        ),
        new OA\Property(
            "updatedAt",
            description: "Update at",
            type: "string",
            format: "date-time"
        ),
        new OA\Property(
            "session",
            description: "Session",
            type: "integer"
        ),
        new OA\Property(
            "learnerGroups",
            description: "Learner groups",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "instructorGroups",
            description: "Instructor groups",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "learners",
            description: "Learners",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "instructors",
            description: "Instructors",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
#[IA\FilterableBy('sessions', IA\Type::INTEGERS)]
#[IA\FilterableBy('courses', IA\Type::INTEGERS)]
#[IA\FilterableBy('schools', IA\Type::INTEGERS)]
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
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $learnerGroups = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $instructorGroups = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $learners = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $instructors = [];

    /**
     * For Voter use, not public
     */
    #[Ignore]
    public int $course;

    /**
     * For Voter use, not public
     */
    #[Ignore]
    public int $school;

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
