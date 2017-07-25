<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class ProgramYearStewardDTO
 *
 * @IS\DTO
 */
class ProgramYearStewardDTO
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
    public $department;

    /**
     * @var integer
     *
     * @IS\Expose
     * @IS\Type("string")
     **/
    public $programYear;

    /**
     * @var integer
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $school;

    /**
     * @var integer
     * Not exposed, used in voter
     */
    public $owningProgram;

    /**
     * @var integer
     * Not exposed, used in voter
     */
    public $owningSchool;

    /**
     * @param $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }
}
