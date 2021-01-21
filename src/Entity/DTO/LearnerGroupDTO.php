<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class LearnerGroupDTO
 * Data transfer object for a learner group
 *
 * @IS\DTO("learnerGroups")
 */
class LearnerGroupDTO
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
     */
    public string $title;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $location;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $url;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $needsAccommodation;

    /**
     * @IS\Expose
     * @IS\Related("cohorts")
     * @IS\Type("string")
     */
    public int $cohort;

    /**
     * @IS\Expose
     * @IS\Related("learnerGroups")
     * @IS\Type("string")
     */
    public ?int $parent;

    /**
     * @IS\Expose
     * @IS\Related("learnerGroups")
     * @IS\Type("string")
     */
    public ?int $ancestor;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("learnerGroups")
     * @IS\Type("array<string>")
     */
    public array $descendants;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("learnerGroups")
     * @IS\Type("array<string>")
     */
    public array $children;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $ilmSessions;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $offerings;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $instructorGroups;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $users;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("array<string>")
     */
    public array $instructors;

    public function __construct(int $id, string $title, ?string $location, ?string $url, bool $needsAccommodation)
    {
        $this->id = $id;
        $this->title = $title;
        $this->location = $location;
        $this->url = $url;
        $this->needsAccommodation = $needsAccommodation;

        $this->children = [];
        $this->ilmSessions = [];
        $this->offerings = [];
        $this->instructorGroups = [];
        $this->users = [];
        $this->instructors = [];
        $this->descendants = [];
    }
}
