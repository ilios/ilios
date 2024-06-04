<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use OpenApi\Attributes as OA;

#[IA\DTO('aamcPcrses')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "AamcPcrs",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "string"
        ),
        new OA\Property(
            "description",
            description: "Description",
            type: "string"
        ),
        new OA\Property(
            "competencies",
            description: "Competencies",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
class AamcPcrsDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('string')]
    public string $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $description;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $competencies = [];

    public function __construct(
        string $id,
        string $description
    ) {
        $this->id = $id;
        $this->description = $description;
    }
}
