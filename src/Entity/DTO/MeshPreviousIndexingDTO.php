<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;

/**
 * Class MeshPreviousIndexingDTO
 * Data transfer object for a MeSH descriptor.
 */
#[IA\DTO('meshPreviousIndexings')]
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
