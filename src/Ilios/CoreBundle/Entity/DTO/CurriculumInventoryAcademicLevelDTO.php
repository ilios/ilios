<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class CurriculumInventoryAcademicLevelDTO
 *
 * @IS\DTO
 */
class CurriculumInventoryAcademicLevelDTO
{
    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     *
    */
    public $name;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
    */
    public $description;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $level;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $report;

    /**
    * @var array
    *
    * @IS\Expose
    * @IS\Type("array<string>")
    */
    public $sequenceBlocks;

    /**
     * Needed for voting not exposed in the API
     *
     * @var integer
     *
     * @IS\Type("integer")
     */
    public $school;

    /**
     * Constructor
     */
    public function __construct($id, $name, $description, $level)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->level = $level;
        
        $this->sequenceBlocks = [];
    }
}
