<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use DateTime;
use OpenApi\Attributes as OA;

#[IA\DTO('meshConcepts')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "MeshConcept",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "string",
            readOnly: true,
        ),
        new OA\Property(
            "name",
            description: "Name",
            type: "string"
        ),
        new OA\Property(
            "preferred",
            description: "Is preferred",
            type: "boolean"
        ),
        new OA\Property(
            "scopeNote",
            description: "Scope note",
            type: "string"
        ),
        new OA\Property(
            "casn1Name",
            description: "CASN1 name",
            type: "string"
        ),
        new OA\Property(
            "registryNumber",
            description: "Registry number",
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
            "terms",
            description: "MeSH terms",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "descriptors",
            description: "MeSH descriptors",
            type: "array",
            items: new OA\Items(type: "string")
        )
    ]
)]
class MeshConceptDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('string')]
    public string $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $name;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $preferred;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $scopeNote;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $casn1Name;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $registryNumber;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $createdAt;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $updatedAt;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('meshTerms')]
    #[IA\Type('array<integer>')]
    public array $terms = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('meshDescriptors')]
    #[IA\Type('array<string>')]
    public array $descriptors = [];

    public function __construct(
        string $id,
        string $name,
        bool $preferred,
        ?string $scopeNote,
        ?string $casn1Name,
        ?string $registryNumber,
        DateTime $createdAt,
        DateTime $updatedAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->preferred = $preferred;
        $this->scopeNote = $scopeNote;
        $this->casn1Name = $casn1Name;
        $this->registryNumber = $registryNumber;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }
}
