<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class CurriculumInventorySequenceBlockDTO
 *
 * @IS\DTO
 */
class CurriculumInventorySequenceDTO
{
    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var integer
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
     * @var integer
     *
     * @IS\Type("integer")
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
