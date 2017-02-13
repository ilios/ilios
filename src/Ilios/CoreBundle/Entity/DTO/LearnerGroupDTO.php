<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class LearnerGroupDTO
 * Data transfer object for a learner group
 * @package Ilios\CoreBundle\Entity\DTO

 */
class LearnerGroupDTO
{
    
    /**
     * @var int
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @IS\Type("string")
     */
    public $title;

    /**
     * @var string
     * @IS\Type("string")
     */
    public $location;

    /**
     * @var int
     * @IS\Type("string")
     */
    public $cohort;

    /**
     * @var int
     * @IS\Type("string")
     */
    public $parent;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $children;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $ilmSessions;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $offerings;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $instructorGroups;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $users;

    /**
     * @var array
     * @IS\Type("entityCollection")
     */
    public $instructors;

    public function __construct(
        $id,
        $title,
        $location
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->location = $location;


        $this->children = [];
        $this->ilmSessions = [];
        $this->offerings = [];
        $this->instructorGroups = [];
        $this->users = [];
        $this->instructors = [];
    }
}
