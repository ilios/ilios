<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use DateTime;
use OpenApi\Attributes as OA;

#[IA\DTO('meshQualifiers')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "MeshQualifier",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "string"
        ),
        new OA\Property(
            "name",
            description: "Name",
            type: "string"
        ),
        new OA\Property(
            "createdAt",
            description: "Created at",
            type: "string",
            format: "date-time"
        ),
        new OA\Property(
            "updatedAt",
            description: "Updated at",
            type: "string",
            format: "date-time"
        ),
        new OA\Property(
            "descriptors",
            description: "MeSH descriptors",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
class MeshQualifierDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('string')]
    public string $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $name;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $createdAt;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $updatedAt;

    /**
     * @var string[]
     */
    #[IA\Expose]
    #[IA\Related('meshDescriptors')]
    #[IA\Type(IA\Type::STRINGS)]
    public array $descriptors = [];

    public function __construct(string $id, string $name, DateTime $createdAt, DateTime $updatedAt)
    {
        $this->id = $id;
        $this->name = $name;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }
}
