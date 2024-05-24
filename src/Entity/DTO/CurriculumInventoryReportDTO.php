<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use DateTime;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Ignore;

#[IA\DTO('curriculumInventoryReports')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "CurriculumInventoryReport",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "integer"
        ),
        new OA\Property(
            "name",
            description: "Name",
            type: "string"
        ),
        new OA\Property(
            "description",
            description: "Description",
            type: "string"
        ),
        new OA\Property(
            "year",
            description: "Year",
            type: "string"
        ),
        new OA\Property(
            "startDate",
            description: "Start date",
            type: "string",
            format: 'date-time'
        ),
        new OA\Property(
            "endDate",
            description: "End date",
            type: "string",
            format: 'date-time'
        ),
        new OA\Property(
            "absoluteFileUri",
            description: "Report download URL",
            type: "string"
        ),
        new OA\Property(
            "program",
            description: "Program",
            type: "integer"
        ),
        new OA\Property(
            "export",
            description: "Report export",
            type: "integer"
        ),
        new OA\Property(
            "sequence",
            description: "Report sequence",
            type: "integer"
        ),
        new OA\Property(
            "sequenceBlocks",
            description: "Sequence blocks",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "academicLevels",
            description: "Academic Levels",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "administrators",
            description: "Administrators",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
class CurriculumInventoryReportDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $name;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $description;

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $year;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $startDate;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $endDate;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $absoluteFileUri;

    #[IA\Expose]
    #[IA\Related('programs')]
    #[IA\Type('integer')]
    public int $program;

    #[IA\Expose]
    #[IA\Related('curriculumInventoryExports')]
    #[IA\Type('integer')]
    public ?int $export = null;

    #[IA\Expose]
    #[IA\Related('curriculumInventorySequences')]
    #[IA\Type('integer')]
    public ?int $sequence = null;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('curriculumInventorySequenceBlocks')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $sequenceBlocks = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('curriculumInventoryAcademicLevels')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $academicLevels = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $administrators = [];

    /**
     * Needed for voting not exposed in the API
     */
    #[Ignore]
    public int $school;

    /**
     * Needed for creating the absolute URL, not exposed in the API
     */
    #[Ignore]
    public ?string $token;

    public function __construct(
        int $id,
        ?string $name,
        ?string $description,
        int $year,
        DateTime $startDate,
        DateTime $endDate,
        ?string $token
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->year = $year;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->token = $token;
    }
}
