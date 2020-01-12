<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

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
     * @var int
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $department;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("string")
     **/
    public $programYear;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $school;

    /**
     * @var int
     * Not exposed, used in voter
     */
    public $owningProgram;

    /**
     * @var int
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
