<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use OpenApi\Attributes as OA;

#[IA\DTO('meshPreviousIndexings')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "MeshPreviousIndexing",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "integer"
        ),
        new OA\Property(
            "previousIndexing",
            description: "Previous indexing",
            type: "string"
        ),
        new OA\Property(
            "descriptor",
            description: "MeSH descriptor",
            type: "string"
        )
    ]
)]
class MeshPreviousIndexingDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $previousIndexing;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $descriptor;

    public function __construct(int $id, string $previousIndexing)
    {
        $this->id = $id;
        $this->previousIndexing = $previousIndexing;
    }
}
