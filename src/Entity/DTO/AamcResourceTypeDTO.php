<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;

/**
 * Class AamcResourceTypeDTO
 * Data transfer object for a aamcResourceType
 */
#[IA\DTO('aamcResourceTypes')]
#[IA\ExposeGraphQL]
class AamcResourceTypeDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('string')]
    public string $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $title;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $description;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $terms = [];

    public function __construct(
        string $id,
        string $title,
        string $description
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
    }
}
