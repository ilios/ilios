<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use OpenApi\Attributes as OA;

#[IA\DTO('assessmentOptions')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "AssessmentOption",
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
            "sessionTypes",
            description: "Session types",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
class AssessmentOptionDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $name;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $sessionTypes = [];

    public function __construct(
        int $id,
        string $name
    ) {
        $this->id = $id;
        $this->name = $name;
    }
}
