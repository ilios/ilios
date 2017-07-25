<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class ProgramDTO
 * Data transfer object for a Program
 *
 * @IS\DTO
 */
class ProgramDTO
{
    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     *
     */
    public $title;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     *
     */
    public $shortTitle;

    /**
     * @var integer
     * @IS\Expose
     * @IS\Type("string")
     *
     */
    public $duration;

    /**
     * @var boolean
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $publishedAsTbd;

    /**
     * @var boolean
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $published;

    /**
     * @var integer
     * @IS\Expose
     * @IS\Type("string")
     *
     */
    public $school;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     *
     */
    public $programYears;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     *
     */
    public $curriculumInventoryReports;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $directors;


    public function __construct(
        $id,
        $title,
        $shortTitle,
        $duration,
        $publishedAsTbd,
        $published
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->shortTitle = $shortTitle;
        $this->duration = $duration;
        $this->publishedAsTbd = $publishedAsTbd;
        $this->published = $published;

        $this->programYears = [];
        $this->curriculumInventoryReports = [];
        $this->directors = [];
    }
}
