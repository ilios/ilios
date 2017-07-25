<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class IlmSessionDTO
 * Data transfer object for a ilmSession
 *
 * @IS\DTO
 */
class IlmSessionDTO
{
    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var integer
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $session;

    /**
     * @var float
     *
     * @IS\Expose
     * @IS\Type("float")
     */
    public $hours;

    /**
     * @var \DateTime
     *
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public $dueDate;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $learnerGroups;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $instructorGroups;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $instructors;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $learners;

    /**
     * Needed for voting not exposed in the API
     *
     * @var integer
     *
     * @IS\Type("integer")
     */
    public $course;

    /**
     * Needed for voting not exposed in the API
     *
     * @var integer
     *
     * @IS\Type("integer")
     */
    public $school;

    /**
     * Constructor
     */
    public function __construct($id, $hours, $dueDate)
    {
        $this->id = $id;
        $this->hours = $hours;
        $this->dueDate = $dueDate;
        
        $this->learnerGroups = [];
        $this->instructors = [];
        $this->instructorGroups = [];
        $this->learners = [];
    }
}
