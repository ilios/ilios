<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class CurriculumInventorySequenceBlockDTO
 *
 * @IS\DTO("curriculumInventorySequences")
 */
class CurriculumInventorySequenceDTO
{
    /**
     * @var int
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $report;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $description;

    /**
     * Needed for voting not exposed in the API
     *
     * @var int
     */
    public $school;

    /**
     * CurriculumInventorySequenceBlockDTO constructor.
     * @param $id
     * @param $description
     */
    public function __construct(
        $id,
        $description
    ) {
        $this->id = $id;
        $this->description = $description;
    }
}
