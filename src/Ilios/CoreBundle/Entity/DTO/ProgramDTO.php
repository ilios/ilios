<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class ProgramDTO
 * Data transfer object for a Program
 * @package Ilios\CoreBundle\Entity\DTO

 */
class ProgramDTO
{
    /**
     * @var int
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @IS\Type("string")
     *
     */
    public $title;

    /**
     * @var string
     * @IS\Type("string")
     *
     */
    public $shortTitle;

    /**
     * @var integer
     * @IS\Type("string")
     *
     */
    public $duration;

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
     * @var integer
     * @IS\Type("string")
     *
     */
    public $school;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     *
     */
    public $programYears;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     *
     */
    public $curriculumInventoryReports;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
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
