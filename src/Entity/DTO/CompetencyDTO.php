<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;

/**
 * Class CompetencyDTO
 * Data transfer object for a competency
 */
#[IA\DTO('competencies')]
class CompetencyDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $title;

    #[IA\Expose]
    #[IA\Related('schools')]
    #[IA\Type('integer')]
    public int $school;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('programYearObjectives')]
    #[IA\Type('array<string>')]
    public array $programYearObjectives = [];

    #[IA\Expose]
    #[IA\Related('competencies')]
    #[IA\Type('integer')]
    public ?int $parent = null;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('competencies')]
    #[IA\Type('array<string>')]
    public array $children = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $aamcPcrses = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $programYears = [];

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $active;

    public function __construct(
        int $id,
        ?string $title,
        bool $active
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->active = $active;
    }
}
