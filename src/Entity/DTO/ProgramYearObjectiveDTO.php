<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;

/**
 * Class ProgramYearObjectiveDTO
 */
#[IA\DTO('programYearObjectives')]
#[IA\ExposeGraphQL]
class ProgramYearObjectiveDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $title;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $active;

    #[IA\Expose]
    #[IA\Related('competencies')]
    #[IA\Type('integer')]
    public ?int $competency = null;

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $position;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $terms = [];

    #[IA\Expose]
    #[IA\Related('programYears')]
    #[IA\Type('integer')]
    public int $programYear;

    /**
     * Needed for Voting, not exposed in the API
     */
    #[IA\Type('boolean')]
    public bool $programYearIsLocked;

    /**
     * Needed for Voting, not exposed in the API
     */
    #[IA\Type('boolean')]
    public bool $programYearIsArchived;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('courseObjectives')]
    #[IA\Type('array<string>')]
    public array $courseObjectives = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $meshDescriptors = [];

    #[IA\Expose]
    #[IA\Related('programYearObjectives')]
    #[IA\Type('integer')]
    public ?int $ancestor = null;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('programYearObjectives')]
    #[IA\Type('array<string>')]
    public array $descendants = [];

    public function __construct(int $id, string $title, int $position, bool $active)
    {
        $this->id = $id;
        $this->title = $title;
        $this->position = $position;
        $this->active = $active;
    }
}
