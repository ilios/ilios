<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use DateTime;
use OpenApi\Attributes as OA;

#[IA\DTO('meshTerms')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "MeshTerm",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "integer"
        ),
        new OA\Property(
            "meshTermUid",
            description: "UID",
            type: "string"
        ),
        new OA\Property(
            "name",
            description: "Name",
            type: "string"
        ),
        new OA\Property(
            "lexicalTag",
            description: "Lexical tag",
            type: "string"
        ),
        new OA\Property(
            "conceptPreferred",
            description: "Is concept preferred",
            type: "boolean"
        ),
        new OA\Property(
            "recordPreferred",
            description: "Is record preferred",
            type: "boolean"
        ),
        new OA\Property(
            "permuted",
            description: "Is permuted",
            type: "boolean"
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
            "concepts",
            description: "MeSH concepts",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
class MeshTermDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $meshTermUid;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $name;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $lexicalTag;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public ?bool $conceptPreferred;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public ?bool $recordPreferred;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public ?bool $permuted;

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
    #[IA\Related('meshConcepts')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $concepts = [];

    public function __construct(
        int $id,
        string $meshTermUid,
        string $name,
        ?string $lexicalTag,
        ?bool $conceptPreferred,
        ?bool $recordPreferred,
        ?bool $permuted,
        DateTime $createdAt,
        DateTime $updatedAt
    ) {
        $this->id = $id;
        $this->meshTermUid = $meshTermUid;
        $this->name = $name;
        $this->lexicalTag = $lexicalTag;
        $this->conceptPreferred = $conceptPreferred;
        $this->recordPreferred = $recordPreferred;
        $this->permuted = $permuted;
        $this->name = $name;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }
}
