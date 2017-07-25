<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class LearnerGroupDTO
 * Data transfer object for a learner group
 *
 * @IS\DTO
 */
class LearnerGroupDTO
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
     */
    public $title;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $location;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $cohort;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $parent;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $children;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $ilmSessions;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $offerings;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $instructorGroups;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $users;

    /**
     * @var array
     * @IS\Expose
     * @IS\Type("array<string>")
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
