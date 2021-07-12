<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;

/**
 * Class CohortDTO
 * Data transfer object for a cohort
 */
#[IA\DTO('cohorts')]
class CohortDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $title;

    #[IA\Expose]
    #[IA\Related('programYears')]
    #[IA\Type('integer')]
    public int $programYear;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $courses = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $learnerGroups = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $users = [];

    /**
     * For Voter use, not public
     */
    public int $program;

    /**
     * For Voter use, not public
     */
    public int $school;

    public function __construct(
        int $id,
        string $title
    ) {
        $this->id = $id;
        $this->title = $title;

        $this->courses = [];
        $this->learnerGroups = [];
        $this->users = [];
    }
}
