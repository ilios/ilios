<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Ignore;

#[IA\DTO('programYears')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "Program",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "integer"
        ),
        new OA\Property(
            "startYear",
            description: "Start year",
            type: "string"
        ),
        new OA\Property(
            "locked",
            description: "Is locked",
            type: "boolean"
        ),
        new OA\Property(
            "archived",
            description: "Is archived",
            type: "boolean"
        ),
        new OA\Property(
            "program",
            description: "Program",
            type: "integer"
        ),
        new OA\Property(
            "cohort",
            description: "Cohort",
            type: "integer"
        ),
        new OA\Property(
            "directors",
            description: "Directors",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "competencies",
            description: "Competencies",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "terms",
            description: "Vocabulary terms",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "programYearObjectives",
            description: "Program year objectives",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
#[IA\FilterableBy('courses', IA\Type::INTEGERS)]
#[IA\FilterableBy('sessions', IA\Type::INTEGERS)]
#[IA\FilterableBy('schools', IA\Type::INTEGERS)]
#[IA\FilterableBy('startYears', IA\Type::INTEGERS)]
class ProgramYearDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $startYear;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $locked;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $archived;

    #[IA\Expose]
    #[IA\Related('programs')]
    #[IA\Type('integer')]
    public int $program;

    #[IA\Expose]
    #[IA\Related('cohorts')]
    #[IA\Type('integer')]
    public int $cohort;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $directors = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $competencies = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $terms = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $programYearObjectives = [];

    /**
     * For Voter use, not public
     */
    #[Ignore]
    public int $school;

    public function __construct(
        int $id,
        int $startYear,
        bool $locked,
        bool $archived
    ) {
        $this->id = $id;
        $this->startYear = $startYear;
        $this->locked = $locked;
        $this->archived = $archived;
    }
}
