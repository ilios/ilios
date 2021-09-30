<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use DateTime;

/**
 * Class MeshTermDTO
 */
#[IA\DTO('meshTerms')]
#[IA\ExposeGraphQL]
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
    #[IA\Type('array<string>')]
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
