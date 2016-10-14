<?php

namespace Ilios\CoreBundle\Entity\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * Class ProgramDTO
 * Data transfer object for a Program
 * @package Ilios\CoreBundle\Entity\DTO

 */
class ProgramDTO
{
    /**
     * @var int
     * @JMS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @JMS\Type("string")
     *
     */
    public $title;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("shortTitle")
     *
     */
    public $shortTitle;

    /**
     * @var integer
     * @JMS\Type("string")
     *
     */
    public $duration;

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
     * @var integer
     * @JMS\Type("string")
     *
     */
    public $school;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("programYears")
     *
     */
    public $programYears;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("curriculumInventoryReports")
     *
     */
    public $curriculumInventoryReports;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
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
