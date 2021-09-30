<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;

/**
 * Class LearnerGroupDTO
 */
#[IA\DTO('learnerGroups')]
#[IA\ExposeGraphQL]
class LearnerGroupDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $title;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $location;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $url;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $needsAccommodation;

    #[IA\Expose]
    #[IA\Related('cohorts')]
    #[IA\Type('integer')]
    public int $cohort;

    #[IA\Expose]
    #[IA\Related('learnerGroups')]
    #[IA\Type('integer')]
    public ?int $parent = null;

    #[IA\Expose]
    #[IA\Related('learnerGroups')]
    #[IA\Type('integer')]
    public ?int $ancestor = null;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('learnerGroups')]
    #[IA\Type('array<string>')]
    public array $descendants = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('learnerGroups')]
    #[IA\Type('array<string>')]
    public array $children = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $ilmSessions = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $offerings = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $instructorGroups = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $users = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type('array<string>')]
    public array $instructors = [];

    public function __construct(int $id, string $title, ?string $location, ?string $url, bool $needsAccommodation)
    {
        $this->id = $id;
        $this->title = $title;
        $this->location = $location;
        $this->url = $url;
        $this->needsAccommodation = $needsAccommodation;
    }
}
