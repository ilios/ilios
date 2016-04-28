<?php

namespace Ilios\CoreBundle\Entity\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * Class LearnerGroupDTO
 * Data transfer object for a learner group
 * @package Ilios\CoreBundle\Entity\DTO

 */
class LearnerGroupDTO
{
    
    /**
     * @var int
     * @JMS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @JMS\Type("string")
     */
    public $title;

    /**
     * @var string
     * @JMS\Type("string")
     */
    public $location;

    /**
     * @var int
     * @JMS\Type("string")
     */
    public $cohort;

    /**
     * @var int
     * @JMS\Type("string")
     */
    public $parent;

    /**
     * @var array
     * @JMS\Type("array<string>")
     */
    public $children;

    /**
     * @var array
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("ilmSessions")
     */
    public $ilmSessions;

    /**
     * @var array
     * @JMS\Type("array<string>")
     */
    public $offerings;

    /**
     * @var array
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("instructorGroups")
     */
    public $instructorGroups;

    /**
     * @var array
     * @JMS\Type("array<string>")
     */
    public $users;

    /**
     * @var array
     * @JMS\Type("array<string>")
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
