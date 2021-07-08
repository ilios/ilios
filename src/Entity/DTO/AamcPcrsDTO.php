<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;

/**
 * Class AamcPcrsDTO
 * Data transfer object for a aamcPcrs
 */
#[IA\DTO('aamcPcrses')]
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
    #[IA\Type('array<string>')]
    public array $competencies = [];

    public function __construct(
        string $id,
        string $description
    ) {
        $this->id = $id;
        $this->description = $description;
    }
}
