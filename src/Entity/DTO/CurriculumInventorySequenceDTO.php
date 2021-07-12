<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class CurriculumInventorySequenceBlockDTO
 * @IS\DTO("curriculumInventorySequences")
 */
class CurriculumInventorySequenceDTO
{
    /**
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $id;

    /**
     * @IS\Expose
     * @IS\Related("curriculumInventoryReport")
     * @IS\Type("integer")
     */
    public int $report;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $description;

    /**
     * Needed for voting not exposed in the API
     */
    public int $school;

    public function __construct(
        int $id,
        ?string $description
    ) {
        $this->id = $id;
        $this->description = $description;
    }
}
