<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class AssessmentOptionDTO
 * Data transfer object for an assessmentOption
 *
 * @IS\DTO("assessmentOptions")
 */
class AssessmentOptionDTO
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
    public string $name;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $sessionTypes = [];

    public function __construct(
        int $id,
        string $name
    ) {
        $this->id = $id;
        $this->name = $name;
    }
}
