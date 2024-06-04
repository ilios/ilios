<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use DateTime;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Ignore;

#[IA\DTO('ilmSessions')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "IlmSession",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "integer"
        ),
        new OA\Property(
            "session",
            description: "Session",
            type: "integer"
        ),
        new OA\Property(
            "hours",
            description: "Duration in hours",
            type: "float"
        ),
        new OA\Property(
            "dueDate",
            description: "Due-date",
            type: "string",
            format: "date-time"
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
            "instructors",
            description: "Instructors",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "learners",
            description: "Learners",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
#[IA\FilterableBy('courses', IA\Type::INTEGERS)]
#[IA\FilterableBy('sessions', IA\Type::INTEGERS)]
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
    public array $instructors = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $learners = [];

    /**
     * Needed for voting not exposed in the API
     */
    #[Ignore]
    public int $course;

    /**
     * Needed for voting not exposed in the API
     */
    #[Ignore]
    public int $school;

    public function __construct(int $id, float $hours, DateTime $dueDate)
    {
        $this->id = $id;
        $this->hours = $hours;
        $this->dueDate = $dueDate;
    }
}
