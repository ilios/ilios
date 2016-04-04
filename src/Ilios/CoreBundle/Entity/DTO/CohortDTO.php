<?php

namespace Ilios\CoreBundle\Entity\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * Class CohortDTO
 * Data transfer object for a cohort
 * @package Ilios\CoreBundle\Entity\DTO

 */
class CohortDTO
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
     * @var int
     * @JMS\Type("string")
     * @JMS\SerializedName("programYear")
     */
    public $programYear;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $courses;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("learnerGroups")
     */
    public $learnerGroups;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $users;

    public function __construct(
        $id,
        $title
    ) {
        $this->id = $id;
        $this->title = $title;

        $this->courses = [];
        $this->learnerGroups = [];
        $this->users = [];
    }
}
