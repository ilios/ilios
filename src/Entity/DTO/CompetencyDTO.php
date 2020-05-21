<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class CompetencyDTO
 * Data transfer object for a competency
 *
 * @IS\DTO("competencies")
 */
class CompetencyDTO
{
    /**
     * @var int
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     *
     */
    public $title;

    /**
     * @var int
     * @IS\Expose
     * @IS\Related("schools")
     * @IS\Type("string")
     */
    public $school;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $objectives;

    /**
     * @var int
     * @IS\Expose
     * @IS\Related("competencies")
     * @IS\Type("string")
     */
    public $parent;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("competencies")
     * @IS\Type("array<string>")
     */
    public $children;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $aamcPcrses;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $programYears;

    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("boolean")
     *
     */
    public $active;

    public function __construct(
        $id,
        $title,
        $active
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->active = $active;

        $this->objectives = [];
        $this->children = [];
        $this->aamcPcrses = [];
        $this->programYears = [];
    }
}
