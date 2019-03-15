<?php


namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class ProgramYearDTO
 * Data transfer object for a programYear
 *
 * @IS\DTO
 */
class ProgramYearDTO
{

    /**
    * @var int
     * @IS\Expose
    * @IS\Type("integer")
    */
    public $id;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $startYear;

    /**
     * @var boolean
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $locked;

    /**
     * @var boolean
     *
     * @IS\Expose
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $archived;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $program;

    /**
    * @var int
     * @IS\Expose
    * @IS\Type("string")
    */
    public $cohort;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $directors;

    /**
     * @var ArrayCollection|CompetencyInterface[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $competencies;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $terms;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $objectives;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $stewards;

    /**
     * For Voter use, not public
     * @var int
     */
    public $school;

    public function __construct(
        $id,
        $startYear,
        $locked,
        $archived
    ) {
        $this->id = $id;
        $this->startYear = $startYear;
        $this->locked = $locked;
        $this->archived = $archived;

        $this->directors = [];
        $this->competencies = [];
        $this->terms = [];
        $this->objectives = [];
        $this->stewards = [];
    }
}
