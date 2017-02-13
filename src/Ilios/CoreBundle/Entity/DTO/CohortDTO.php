<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class CohortDTO
 * Data transfer object for a cohort
 * @package Ilios\CoreBundle\Entity\DTO

 */
class CohortDTO
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
     * @var int
     * @IS\Type("string")
     */
    public $programYear;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $courses;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $learnerGroups;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
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
