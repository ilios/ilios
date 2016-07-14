<?php


namespace Ilios\CoreBundle\Entity\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * Class ProgramYearDTO
 * Data transfer object for a programYear
 * @package Ilios\CoreBundle\Entity\DTO

 */
class ProgramYearDTO
{

    /**
    * @var int
    * @JMS\Type("integer")
    */
    public $id;

    /**
     * @var int
     * @JMS\Type("string")
     * @JMS\SerializedName("startYear")
     */
    public $startYear;

    /**
     * @var boolean
     * @JMS\Type("boolean")
     */
    public $locked;

    /**
     * @var boolean
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    public $archived;

    /**
     * @var boolean
     * @JMS\Type("boolean")
     * @JMS\SerializedName("publishedAsTbd")
     */
    public $publishedAsTbd;

    /**
     * @var boolean
     * @JMS\Type("boolean")
     */
    public $published;

    /**
     * @var int
     * @JMS\Type("string")
     */
    public $program;

    /**
    * @var int
    * @JMS\Type("string")
    */
    public $cohort;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $directors;

    /**
     * @var ArrayCollection|CompetencyInterface[]
     * @JMS\Type("array<string>")
     */
    public $competencies;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $terms;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $objectives;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
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
