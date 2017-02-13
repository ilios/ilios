<?php


namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class ProgramYearDTO
 * Data transfer object for a programYear
 * @package Ilios\CoreBundle\Entity\DTO

 */
class ProgramYearDTO
{

    /**
    * @var int
    * @IS\Type("integer")
    */
    public $id;

    /**
     * @var int
     * @IS\Type("string")
     */
    public $startYear;

    /**
     * @var boolean
     * @IS\Type("boolean")
     */
    public $locked;

    /**
     * @var boolean
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $archived;

    /**
     * @var boolean
     * @IS\Type("boolean")
     */
    public $publishedAsTbd;

    /**
     * @var boolean
     * @IS\Type("boolean")
     */
    public $published;

    /**
     * @var int
     * @IS\Type("string")
     */
    public $program;

    /**
    * @var int
    * @IS\Type("string")
    */
    public $cohort;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $directors;

    /**
     * @var ArrayCollection|CompetencyInterface[]
     * @IS\Type("entityCollection")
     */
    public $competencies;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $terms;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $objectives;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $stewards;

    public function __construct(
        $id,
        $startYear,
        $locked,
        $archived,
        $publishedAsTbd,
        $published
    ) {
        $this->id = $id;
        $this->startYear = $startYear;
        $this->locked = $locked;
        $this->archived = $archived;
        $this->publishedAsTbd = $publishedAsTbd;
        $this->published = $published;

        $this->directors = [];
        $this->competencies = [];
        $this->terms = [];
        $this->objectives = [];
        $this->stewards = [];
    }
}
