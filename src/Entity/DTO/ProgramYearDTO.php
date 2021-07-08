<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use App\Entity\CompetencyInterface;

/**
 * Class ProgramYearDTO
 * Data transfer object for a programYear
 */
#[IA\DTO('programYears')]
class ProgramYearDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public int $startYear;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $locked;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $archived;

    #[IA\Expose]
    #[IA\Related('programs')]
    #[IA\Type('integer')]
    public int $program;

    #[IA\Expose]
    #[IA\Related('cohorts')]
    #[IA\Type('integer')]
    public int $cohort;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type('array<string>')]
    public array $directors = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $competencies = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $terms = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $programYearObjectives = [];

    /**
     * For Voter use, not public
     */
    public int $school;

    public function __construct(
        int $id,
        int $startYear,
        bool $locked,
        bool $archived
    ) {
        $this->id = $id;
        $this->startYear = $startYear;
        $this->locked = $locked;
        $this->archived = $archived;
    }
}
