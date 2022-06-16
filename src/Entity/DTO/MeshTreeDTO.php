<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use OpenApi\Attributes as OA;

#[IA\DTO('meshTrees')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "MeshTree",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "integer"
        ),
        new OA\Property(
            "treeNumber",
            description: "Tree number",
            type: "string"
        ),
        new OA\Property(
            "descriptor",
            description: "MeSH descriptor",
            type: "string"
        )
    ]
)]
class MeshTreeDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $treeNumber;

    #[IA\Expose]
    #[IA\Related('meshDescriptors')]
    #[IA\Type('string')]
    public string $descriptor;

    public function __construct(int $id, string $treeNumber)
    {
        $this->id = $id;
        $this->treeNumber = $treeNumber;
    }
}
