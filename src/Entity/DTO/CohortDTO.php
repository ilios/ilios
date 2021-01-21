<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class CohortDTO
 * Data transfer object for a cohort
 *
 * @IS\DTO("cohorts")
 */
class CohortDTO
{
    /**
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $id;

    /**
     * @IS\Expose
     * @IS\Type("string")
     *
     */
    public string $title;

    /**
     * @IS\Expose
     * @IS\Related("programYears")
     * @IS\Type("string")
     */
    public int $programYear;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $courses;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $learnerGroups;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $users;


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
